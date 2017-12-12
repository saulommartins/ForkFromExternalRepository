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
    * Classe de mapeamento da tabela Exportacao TCERJ.plano_contas
    * Data de Criação: 26/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-28 11:18:27 -0300 (Sex, 28 Jul 2006) $

    * Casos de uso: uc-02.08.16
*/

/*
$Log$
Revision 1.1  2006/07/28 14:15:06  cako
Bug #6568#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERJAjustesContaCont extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJAjustesContaCont()
{
    parent::Persistente();
    $this->setTabela('tcerj.plano_conta');

    $this->setCampoCod('exercicio');
    $this->setCampoCod('cod_conta');

    $this->setComplementoChave('exercicio,cod_conta');

    $this->AddCampo('exercicio'     ,'char'     ,true,  '04' ,true  ,false);
    $this->AddCampo('cod_conta'     ,'INTEGER'  ,true,  ''   ,true  ,false);
    $this->AddCampo('cod_sequencial','INTEGER'  ,true,  ''   ,false ,false);

}

function montaRecuperaDadosAjustesTC()
{
    $stSql .= "SELECT                                                               \n";
    $stSql .= "    pc.cod_conta as cod_conta,                                       \n";
    $stSql .= "    pc.nom_conta as nom_conta,                                       \n";
    $stSql .= "    pc.cod_estrutural as cod_estrutural,                             \n";
    $stSql .= "    tc.cod_sequencial as cod_sequencial                              \n";
    $stSql .= "FROM                                                                 \n";
    $stSql .= "    contabilidade.plano_conta as pc                                  \n";
    $stSql .= "    LEFT JOIN tcerj.plano_conta as tc ON                             \n";
    $stSql .= "            ( tc.exercicio = pc.exercicio AND                        \n";
    $stSql .= "              tc.cod_conta = pc.cod_conta )                          \n";
    $stSql .= "WHERE pc.exercicio = '".$this->getDado('exercicio')."' AND           \n";
    $stSql .= "          publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->getDado('cod_estrutural')."')||'%') \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosAjustesTC.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/

function recuperaDadosAjustesTC(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY pc.cod_estrutural ";
    $stSql = $this->montaRecuperaDadosAjustesTC().$stCondicao.$stOrdem;
    $this->setDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
