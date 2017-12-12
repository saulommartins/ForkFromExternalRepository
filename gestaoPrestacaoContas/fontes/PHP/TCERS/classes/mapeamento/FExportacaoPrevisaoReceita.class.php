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
    * Classe de mapeamento da tabela FN_EXPORTACAO_PREVISAO_RECEITA
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.8  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoPrevisaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoPrevisaoReceita()
{
    parent::Persistente();
    $this->setTabela('tcerj.fn_exportacao_prevrec');
    $this->AddCampo('exercicio'             ,'character(4)'               ,false,'',false,false);
    $this->AddCampo('cod_estrutural'        ,'integer'                    ,false,'',false,false);
    $this->AddCampo('valor'                 ,'numeric'                    ,false,'',false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "                           exercicio            ,                        \n";
    $stSql .= "                           cod_estrutural       ,                        \n";
    $stSql .= "                           replace(valor,'.','') as valor                \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "        ".$this->getTabela()."('".$this->getDado("stExercicio")."',      \n";
    $stSql .= "                        '".$this->getDado("stCodEntidades")."',          \n";
    $stSql .= "                        '".$this->getDado("dtInicial")."',               \n";
    $stSql .= "                        '".$this->getDado("dtFinal")."')                 \n";
    $stSql .= "AS tabela              (                                                 \n";
    $stSql .= "                           exercicio            character(4),            \n";
    $stSql .= "                           cod_estrutural       integer,                 \n";
    $stSql .= "                           valor                numeric                  \n";
    $stSql .= "                           )                                             \n";

    return $stSql;
}

}
