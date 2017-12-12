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
  * Classe de mapeamento da tabela BENEFICIO.FAIXA_DESCONTO
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
  * Efetua conexão com a tabela  BENEFICIO.FAIXA_DESCONTO
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioFaixaDesconto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioFaixaDesconto()
{
    parent::Persistente();
    $this->setTabela('beneficio.faixa_desconto');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_faixa,cod_vigencia');

    $this->AddCampo('cod_faixa','integer',true,'',true,false);
    $this->AddCampo('cod_vigencia','integer',true,'',true,true);
    $this->AddCampo('vl_inicial','numeric',true,'14,2,',false,false);
    $this->AddCampo('vl_final','numeric',true,'14,2,',false,false);
    $this->AddCampo('percentual_desconto','numeric',true,'5,2',false,false);

}

function recuperaUltimaVigencia(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaUltimaVigencia().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUltimaVigencia()
{
    $stSql  .= "SELECT                                                      \r\n";
    $stSql  .= "    fd.vl_inicial,                                          \r\n";
    $stSql  .= "    fd.vl_final,                                            \r\n";
    $stSql  .= "    fd.percentual_desconto                                  \r\n";
    $stSql  .= "FROM                                                        \r\n";
    $stSql  .= "    beneficio.vigencia as bv,                           \r\n";
    $stSql  .= "    beneficio.faixa_desconto as fd                      \r\n";
    $stSql  .= "WHERE                                                       \r\n";
    $stSql  .= "    bv.cod_vigencia = fd.cod_vigencia                       \r\n";
    $stSql  .= "    AND bv.vigencia in (select max(vigencia) from beneficio.vigencia) \r\n";

    return $stSql;
}
}
