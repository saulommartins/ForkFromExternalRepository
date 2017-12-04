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
  * Classe de mapeamento da tabela PESSOAL.CASO_CAUSA
  * Data de Criação: 04/05/2005

  * @author Analista: Leandro OLiveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso :uc-04.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CASO_CAUSA
  * Data de Criação: 04/05/2005

  * @author Analista: Leandro OLiveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCasoCausa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCasoCausa()
{
    parent::Persistente();
    $this->setTabela('pessoal.caso_causa');

    $this->setCampoCod('cod_caso_causa');
    $this->setComplementoChave('');

    $this->AddCampo('cod_caso_causa','sequence',true,'',true,false);
    $this->AddCampo('cod_periodo','integer',true,'',false,true);
    $this->AddCampo('cod_causa_rescisao','integer',true,'',false,true);
    $this->AddCampo('descricao','varchar',true,'80',false,false);
    $this->AddCampo('paga_aviso_previo','boolean',true,'',false,false);
    $this->AddCampo('paga_ferias_vencida','boolean',true,'',false,false);
    $this->AddCampo('cod_saque_fgts','char',true,'10,',false,false);
    $this->AddCampo('perc_cont_social','numeric',false,'5,2,',false,false);
    $this->AddCampo('multa_fgts','numeric',false,'5,2,',false,false);
    $this->AddCampo('inc_fgts_ferias','boolean',true,'',false,false);
    $this->AddCampo('inc_fgts_aviso_previo','boolean',true,'',false,false);
    $this->AddCampo('inc_fgts_13','boolean',true,'',false,false);
    $this->AddCampo('inc_irrf_ferias','boolean',true,'',false,false);
    $this->AddCampo('inc_irrf_aviso_previo','boolean',true,'',false,false);
    $this->AddCampo('inc_irrf_13','boolean',true,'',false,false);
    $this->AddCampo('inc_prev_ferias','boolean',true,'',false,false);
    $this->AddCampo('inc_prev_aviso_previo','boolean',true,'',false,false);
    $this->AddCampo('inc_prev_13','boolean',true,'',false,false);
    $this->AddCampo('paga_ferias_proporcional','boolean',true,'',false,false);
    $this->AddCampo('inden_art_479','boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                               \n";
    $stSQL .= "     pcc.*,                                           \n";
    $stSQL .= "     pcr.num_causa                                    \n";
    $stSQL .= " FROM                                                 \n";
    $stSQL .= "    pessoal.caso_causa as pcc,                    \n";
    $stSQL .= "    pessoal.causa_rescisao as pcr                 \n";
    $stSQL .= " WHERE                                                \n";
    $stSQL .= "     pcc.cod_causa_rescisao = pcr.cod_causa_rescisao  \n";

    return $stSQL;
}

function recuperaRelacionamentoSubDivisao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoSubDivisao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoSubDivisao()
{
    $stSql .= "SELECT caso_causa.*                                                          \n";
    $stSql .= "  FROM pessoal.caso_causa                                                    \n";
    $stSql .= "     , pessoal.caso_causa_sub_divisao                                        \n";
    $stSql .= "     , pessoal.periodo_caso                                                  \n";
    $stSql .= " WHERE caso_causa.cod_caso_causa = caso_causa_sub_divisao.cod_caso_causa     \n";
    $stSql .= "   AND caso_causa.cod_periodo = periodo_caso.cod_periodo                     \n";

    return $stSql;
}

function recuperaRelacionamentoContrato(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoContrato().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoContrato()
{
    $stSql .= "SELECT caso_causa.*                                                          \n";
    $stSql .= "  FROM pessoal.caso_causa                                                    \n";
    $stSql .= "     , pessoal.contrato_servidor_caso_causa                                        \n";
    $stSql .= " WHERE caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa     \n";

    return $stSql;
}

}
