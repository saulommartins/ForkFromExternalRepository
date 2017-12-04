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
    * Classe de mapeamento da tabela ExportacaoTCERJ.CONTA_DESPESA
    * Data de Criação: 11/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.10
*/

/*
$Log$
Revision 1.1  2007/09/24 20:02:56  hboaventura
Ticket#10234#

Revision 1.10  2006/07/05 20:45:58  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERJContaDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJContaDespesa()
{
    parent::Persistente();
    $this->setTabela('tcerj.conta_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_conta,exercicio');

    $this->AddCampo('cod_conta','INTEGER',true,'',true,true);
    $this->AddCampo('exercicio','INTEGER',true,'',true,true);
    $this->AddCampo('cod_estrutural_tce','CHAR(08)',true,'',false,false);
    $this->AddCampo('lancamento','BOOLEAN',true,'',false,false);

}

function montaRecuperaDadosArqDespesa()
{
    $stSQL  = " SELECT                                                  \n";
    $stSQL .= "   ocd.exercicio,                                        \n";
    $stSQL .= "   ocd.cod_conta,                                        \n";
    $stSQL .= "   substr(replace(ocd.cod_estrutural,'.',''),1,8) as cod_estrutural,    \n";
    $stSQL .= "   ocd.descricao,                                        \n";
    $stSQL .= "   CASE WHEN tcd.cod_estrutural_tce is null              \n";
    $stSQL .= "     THEN substr(replace(ocd.cod_estrutural,'.',''),1,8) \n";
    $stSQL .= "     ELSE tcd.cod_estrutural_tce                         \n";
    $stSQL .= "   END as cod_estrutural_tce,                            \n";
    $stSQL .= "   tcd.lancamento                                        \n";
    $stSQL .= " FROM                                                    \n";
    $stSQL .= "   orcamento.conta_despesa as ocd                        \n";
    $stSQL .= "   LEFT JOIN tcerj.conta_despesa as tcd on(              \n";
    $stSQL .= "       ocd.exercicio   = tcd.exercicio                   \n";
    $stSQL .= "   AND ocd.cod_conta   = tcd.cod_conta)                  \n";
    $stSQL .= " WHERE ocd.exercicio = '".$this->getDado("exercicio")."' \n";
    $stSQL .= "   AND length(replace(publico.fn_mascarareduzida(ocd.cod_estrutural),'.',''))<=8     \n";
    $stSQL .= " GROUP BY cod_estrutural,ocd.exercicio,ocd.cod_conta,    \n";
    $stSQL .= "          ocd.descricao,tcd.cod_estrutural_tce,tcd.lancamento  \n";
    $stSQL .= " ORDER BY ocd.cod_estrutural                             \n";
    //echo $stSQL;
    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaArqDespesa.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosArqDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosArqDespesa().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
}
