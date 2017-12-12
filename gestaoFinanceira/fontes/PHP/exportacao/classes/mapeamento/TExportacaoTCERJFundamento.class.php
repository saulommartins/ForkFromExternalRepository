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
    * Classe de mapeamento da tabela ExportacaoTCERJ.FUNDAMENTO
    * Data de Criação: 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.12
*/

/*
$Log$
Revision 1.9  2006/07/05 20:45:58  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERJFundamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJFundamento()
{
    parent::Persistente();
    $this->setTabela('tcerj.fundamento');

    $this->setCampoCod('cod_tipo_norma');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo_norma','INTEGER',true,'',true,true);
    $this->AddCampo('cod_fundamento_legal','INTEGER',true,'',false,false);

}

function montaRecuperaDadosArqFundamento()
{
    $stSQL  = " SELECT                                                          \n";
    $stSQL .= "   ntn.cod_tipo_norma,                                           \n";
    $stSQL .= "   ntn.nom_tipo_norma,                                           \n";
    $stSQL .= "   CASE WHEN tfu.cod_fundamento_legal is null                    \n";
    $stSQL .= "     THEN ntn.cod_tipo_norma                                     \n";
    $stSQL .= "     ELSE tfu.cod_fundamento_legal                               \n";
    $stSQL .= "   END as cod_fundamento_legal                                   \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "   normas.tipo_norma as ntn                                      \n";
    $stSQL .= "   LEFT JOIN tcerj.fundamento tfu on(                            \n";
    $stSQL .= "       ntn.cod_tipo_norma = tfu.cod_tipo_norma)                  \n";
    $stSQL .= " ORDER BY ntn.cod_tipo_norma                                     \n";

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
function recuperaDadosArqFundamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosArqFundamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
}
