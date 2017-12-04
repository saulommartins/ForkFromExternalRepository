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
  * Classe de mapeamento da tabela ECONOMICO.NIVEL_CNAE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMNivelCnae.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.NIVEL_CNAE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMNivelCnae extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMNivelCnae()
{
    parent::Persistente();
    $this->setTabela('economico.nivel_cnae');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_nivel,cod_vigencia');

    $this->AddCampo('cod_nivel','integer',true,'',true,false);
    $this->AddCampo('cod_vigencia','integer',true,'',true,true);
    $this->AddCampo('nom_nivel','varchar',true,'40',false,false);
    $this->AddCampo('mascara','varchar',true,'10',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaNiveisVigencia(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNiveisVigencia().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaNiveisVigencia()
{
    $stSql  .= "    SELECT                                                        \n";
    $stSql  .= "        NA.cod_nivel,                                             \n";
    $stSql  .= "        NA.nom_nivel,                                             \n";
    $stSql  .= "        NA.mascara,                                               \n";
    $stSql  .= "        VA.cod_vigencia,                                          \n";
    $stSql  .= "        TO_CHAR(VA.dt_inicio,'DD/MM/YYYY') AS dt_inicio           \n";
    $stSql  .= "    FROM                                                          \n";
    $stSql  .= "        economico.nivel_cnae AS NA,                              \n";
    $stSql  .= "        economico.vigencia_cnae AS VA                            \n";
    $stSql  .= "    WHERE                                                         \n";
    $stSql  .= "        NA.cod_vigencia = VA.cod_vigencia                         \n";

    return $stSql;
}

}
