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
    * Classe de mapeamento da tabela FN_EXPORTACAO_CONTACONT
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

Revision 1.11  2006/07/28 14:18:15  cako
Bug #6568#

Revision 1.10  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoPlanoContas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoPlanoContas()
{
    parent::Persistente();
    $this->setTabela('tcerj.fn_exportacao_contacont');
    $this->AddCampo('exercicio'            ,'character(4)'               ,false,'',false,false);
    $this->AddCampo('cod_estrutural'       ,'integer'                    ,false,'',false,false);
    $this->AddCampo('tipo_conta'           ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_superior'         ,'integer'                    ,false,'',false,false);
    $this->AddCampo('cod_conta'            ,'integer'                    ,false,'',false,false);
    $this->AddCampo('nom_conta'            ,'character varying(160)'     ,false,'',false,false);
    $this->AddCampo('nivel'                ,'integer'                    ,false,'',false,false);
    $this->AddCampo('recebe_lancamento'    ,'text'                       ,false,'',false,false);
    $this->AddCampo('cod_banco'            ,'character varying(5)'       ,false,'',false,false);
    $this->AddCampo('cod_agencia'          ,'character varying(10)'      ,false,'',false,false);
    $this->AddCampo('conta_corrente'       ,'character varying(30)'      ,false,'',false,false);
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
/*  $stSql .= "CREATE TEMP SEQUENCE seq;                                                \n";
    $stSql .= "SELECT                                                                   \n";
    $stSql .= "     nextval('seq') as sequencial,                                       \n";
    $stSql .= "     *                                                                   \n";
    $stSql .= "FROM (                                                                   \n";  */
    $stSql .= " SELECT                                                                  \n";
    $stSql .= "     exercicio            ,                                              \n";
    $stSql .= "     cod_estrutural       ,                                              \n";
    $stSql .= "     tipo_conta           ,                                              \n";
    $stSql .= "     rpad(cod_superior,15,0) as cod_superior,                            \n";
    $stSql .= "     cod_conta            ,                                              \n";
 // $stSql .= "     nom_conta            ,                                              \n";
    $stSql .= "     replace(replace(nom_conta,'\r\n',''),'\n','') as nom_conta,         \n";
    $stSql .= "     nivel                ,                                              \n";
    $stSql .= "     'M' as origem_saldo  ,                                              \n";
    $stSql .= "     CASE WHEN recebe_lancamento = 'sim' THEN                            \n";
    $stSql .= "         1                                                               \n";
    $stSql .= "     ELSE                                                                \n";
    $stSql .= "         2                                                               \n";
    $stSql .= "     END AS recebe_lancamento,                                           \n";
    $stSql .= "     CASE WHEN cod_banco is NULL THEN                                    \n";
    $stSql .= "         '   0'                                                          \n";
    $stSql .= "     ELSE                                                                \n";
    $stSql .= "         cod_banco                                                       \n";
    $stSql .= "     END AS cod_banco,                                                   \n";
    $stSql .= "     CASE WHEN cod_agencia is NULL THEN                                  \n";
    $stSql .= "         '0           '                                                  \n";
    $stSql .= "     ELSE                                                                \n";
    $stSql .= "         cod_agencia                                                     \n";
    $stSql .= "     END AS cod_agencia,                                                 \n";
    $stSql .= "     CASE WHEN conta_corrente is NULL THEN                               \n";
    $stSql .= "         '0         '                                                    \n";
    $stSql .= "     ELSE                                                                \n";
    $stSql .= "         conta_corrente                                                  \n";
    $stSql .= "     END AS conta_corrente,                                              \n";
    $stSql .= "     sequencial                                                          \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "     " . $this->getTabela() . "('".$this->getDado("stExercicio")    ."') \n";
    $stSql .= "AS tabela              (   exercicio            character(4),            \n";
    $stSql .= "                           cod_estrutural       text,                    \n";
    $stSql .= "                           tipo_conta           integer,                 \n";
    $stSql .= "                           cod_superior         text,                    \n";
    $stSql .= "                           cod_conta            integer,                 \n";
    $stSql .= "                           nom_conta            character varying(160),  \n";
    $stSql .= "                           nivel                integer,                 \n";
    $stSql .= "                           recebe_lancamento    text,                    \n";
    $stSql .= "                           cod_banco            character varying(5),    \n";
    $stSql .= "                           cod_agencia          character varying(10),   \n";
    $stSql .= "                           conta_corrente       character varying(30),   \n";
    $stSql .= "                           sequencial           integer                  \n";
    $stSql .= "                        )                                                \n";
//  $stSql .= " ORDER BY cod_estrutural                                                 \n";
//  $stSql .= ") as tabela                                                              \n";
    return $stSql;
}

}
