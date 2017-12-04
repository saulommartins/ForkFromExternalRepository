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
    * Classe de mapeamento da tabela FN_EXPORTACAO_ESTORNO
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.9  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoEstorno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoEstorno()
{
    parent::Persistente();
    $this->setTabela('tcerj.fn_exportacao_estornemp');
    $this->AddCampo('num_unidade'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('exercicio_empenho'     ,'character(4)'               ,false,'',false,false);
    $this->AddCampo('cod_entidade'          ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_empenho'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('stData'                ,'text'                       ,false,'',false,false);
    $this->AddCampo('motivo'                ,'text'                       ,false,'',false,false);
    $this->AddCampo('valor'                 ,'numeric'                    ,false,'',false,false);
    $this->AddCampo('despesa_liquidada'     ,'numeric'                    ,false,'',false,false);
    $this->AddCampo('num_orgao'             ,'integer'                    ,false,'',false,false);
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
    $stSql .= "     num_unidade          ,                                              \n";
//  $stSql .= "     substr(exercicio,3,2)||lpad(cod_entidade,2,0)||lpad(cod_empenho,6,0) as num_empenho,    \n";
    $stSql .= "     lpad(cod_entidade,2)||lpad(cod_empenho,6,0) as num_empenho,         \n";
    $stSql .= "     exercicio    ,                                                      \n";
    $stSql .= "     cod_entidade         ,                                              \n";
    $stSql .= "     cod_empenho          ,                                              \n";
    $stSql .= "     stData               ,                                              \n";
    $stSql .= "     motivo               ,                                              \n";
    $stSql .= "     replace(valor,'.','') as valor,                                     \n";
    $stSql .= "     despesa_liquidada    ,                                              \n";
    $stSql .= "     num_orgao                                                           \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "        ".$this->getTabela()."('".$this->getDado("stExercicio")."',      \n";
    $stSql .= "                               '".$this->getDado("stCodEntidades")."',   \n";
    $stSql .= "                               '".$this->getDado("dtInicial")."',        \n";
    $stSql .= "                               '".$this->getDado("dtFinal")."')          \n";
    $stSql .= "AS tabela              (   num_unidade          integer,                 \n";
    $stSql .= "                           exercicio            character(4),            \n";
    $stSql .= "                           cod_entidade         integer,                 \n";
    $stSql .= "                           cod_empenho          integer,                 \n";
    $stSql .= "                           stData               text,                    \n";
    $stSql .= "                           motivo               text,                    \n";
    $stSql .= "                           valor                numeric,                 \n";
    $stSql .= "                           despesa_liquidada    numeric,                 \n";
    $stSql .= "                           num_orgao            integer                  \n";
    $stSql .= "                           )                                             \n";

    return $stSql;
}

}
