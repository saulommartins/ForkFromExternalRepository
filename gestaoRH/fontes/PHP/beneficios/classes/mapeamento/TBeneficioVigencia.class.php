<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Classe de mapeamento da tabela BENEFICIO.VIGENCIA
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso: uc-04.06.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.VIGENCIA
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioVigencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioVigencia()
{
    parent::Persistente();
    $this->setTabela('beneficio.vigencia');

    $this->setCampoCod('cod_vigencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_vigencia','integer',true , ''  ,true  ,false);
    $this->AddCampo('vigencia'    ,'date'   ,true , ''  ,false ,false);
    $this->AddCampo('tipo'        ,'char'   ,true , '1,',false ,false);
    $this->AddCampo('cod_norma'   ,'integer',true , ''  ,true  ,false);

}

function recuperaBeneficio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaBeneficio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBeneficio()
{
    $stSQL .= " SELECT                                                 \n";
    $stSQL .= "  norma.cod_tipo_norma                                  \n";
    $stSQL .= " ,vigencia.cod_vigencia                                 \n";
    $stSQL .= " ,vigencia.tipo                                         \n";
    $stSQL .= " ,vigencia.cod_norma                                    \n";
    $stSQL .= " ,to_char(vigencia.vigencia, 'dd/mm/yyyy') as vigencia  \n";
    $stSQL .= " FROM                                                   \n";
    $stSQL .= "     beneficio.vigencia                                 \n";
    $stSQL .= " LEFT JOIN                                              \n";
    $stSQL .= "     normas.norma                                       \n";
    $stSQL .= " ON norma.cod_norma = vigencia.cod_norma                \n";

   return $stSQL;

}

}
