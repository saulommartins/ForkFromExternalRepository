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
    * Classe de mapeamento da tabela FN_ExportacaoTCERS_EXPORTACAO_BALANCETE_RECEITA
    * Data de Criação: 10/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.10  2006/07/05 20:45:58  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERSRDExtra extends Persistente
{
function TExportacaoTCERSRDExtra()
{
    parent::Persistente();
    $this->setTabela('tcers.rd_extra');
    $this->setComplementoChave('cod_conta,exercicio');

    $this->AddCampo('cod_conta','integer',true,'',true,true);
    $this->AddCampo('exercicio','varchar',true,'4',true,true);
    $this->AddCampo('classificacao','integer',true,'',false,false);
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
    public function recuperaDadosRDExtra(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->MontaRecuperaDadosRDExtra().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosRDExtra()
    {
        $stSql  = "";
        $stSql .= "SELECT                                                   \n";
        $stSql .= "     TR.classificacao,                                   \n";
        $stSql .= "     PC.cod_estrutural,                                  \n";
        $stSql .= "     PC.nom_conta                                        \n";
        $stSql .= "FROM                                                     \n";
        $stSql .= "     tcers.rd_extra AS TR,                           \n";
        $stSql .= "     contabilidade.plano_conta AS PC                 \n";
        $stSql .= "WHERE                                                    \n";
        $stSql .= "     TR.exercicio = ".$this->getDado("exercicio")." AND  \n";
        $stSql .= "     TR.exercicio = PC.exercicio AND                     \n";
        $stSql .= "     TR.cod_conta = PC.cod_conta                         \n";

        return $stSql;
    }

}
