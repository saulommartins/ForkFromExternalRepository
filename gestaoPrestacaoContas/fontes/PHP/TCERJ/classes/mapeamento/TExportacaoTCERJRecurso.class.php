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
    * Classe de mapeamento da tabela ExportacaoTCERJ.RECURSO
    * Data de Criação: 11/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.11
*/

/*
$Log$
Revision 1.1  2007/09/24 20:02:56  hboaventura
Ticket#10234#

Revision 1.9  2006/07/05 20:45:58  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERJRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJRecurso()
{
    parent::Persistente();
    $this->setTabela('tcerj.recurso');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_recurso,exercicio');

    $this->AddCampo('cod_recurso','INTEGER',true,'',true,true);
    $this->AddCampo('exercicio','CHAR(04)',true,'',true,true);
    $this->AddCampo('cod_fonte','INTEGER',true,'',false,false);

}

function montaRecuperaDadosArqRecurso()
{
    $stSQL  = " SELECT                                                          \n";
    $stSQL .= "   ore.exercicio,                                                \n";
    $stSQL .= "   ore.cod_recurso,                                              \n";
    $stSQL .= "   ore.nom_recurso,                                              \n";
    $stSQL .= "   CASE WHEN tre.cod_fonte is null                               \n";
    $stSQL .= "     THEN ore.cod_recurso                                        \n";
    $stSQL .= "     ELSE tre.cod_fonte                                          \n";
    $stSQL .= "   END as cod_fonte                                              \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "   orcamento.recurso as ore                                      \n";
    $stSQL .= "   LEFT JOIN tcerj.recurso as tre on(                            \n";
    $stSQL .= "       ore.exercicio     = tre.exercicio                         \n";
    $stSQL .= "   AND ore.cod_recurso   = tre.cod_recurso)                      \n";
    $stSQL .= " WHERE ore.exercicio = '".$this->getDado("exercicio")."'         \n";
    $stSQL .= " ORDER BY ore.cod_recurso                                        \n";
    //echo $stSQL;
    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaArqRecurso.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosArqRecurso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosArqRecurso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
}
