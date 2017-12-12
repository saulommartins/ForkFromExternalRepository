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
    * Classe de mapeamento da tabela ExportacaoTCERJ.TIPO_ALTERACAO
    * Data de Criação: 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.13
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

class TExportacaoTCERJTipoAlteracao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJTipoAlteracao()
{
    parent::Persistente();
    $this->setTabela('tcerj.tipo_alteracao');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_tipo,tipo');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('cod_tipo_alteracao','integer',true,'',false,false);

}

function montaRecuperaDadosArqTipoAlteracao()
{
    $stSQL  = " SELECT                                                          \n";
    $stSQL .= "   ctt.exercicio,                                                \n";
    $stSQL .= "   ctt.cod_tipo,                                                 \n";
    $stSQL .= "   CASE WHEN tta.tipo ='R' THEN nom_tipo||' (Reduções)'          \n";
    $stSQL .= "   WHEN tta.tipo='S' AND tta.cod_tipo in(1,6,12,13,14,15) THEN nom_tipo||' (Suplementações)'  \n";
    $stSQL .= "   ELSE nom_tipo                                                 \n";
    $stSQL .= "   END as nom_tipo,                                              \n";
    $stSQL .= "   CASE WHEN tta.cod_tipo_alteracao is null                      \n";
    $stSQL .= "     THEN ctt.cod_tipo                                           \n";
    $stSQL .= "     ELSE tta.cod_tipo_alteracao                                 \n";
    $stSQL .= "   END as cod_tipo_alteracao,                                    \n";
    $stSQL .= "   tta.tipo                                                      \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "   contabilidade.tipo_transferencia as ctt                       \n";
    $stSQL .= "   LEFT JOIN tcerj.tipo_alteracao as tta on(                     \n";
    $stSQL .= "       ctt.exercicio     = tta.exercicio                         \n";
    $stSQL .= "   AND ctt.cod_tipo      = tta.cod_tipo)                         \n";
    $stSQL .= " WHERE ctt.exercicio = '".$this->getDado("exercicio")."'         \n";
    $stSQL .= " ORDER BY ctt.cod_tipo                                           \n";
    //echo $stSQL;
    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaArqTipoAlteracao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosArqTipoAlteracao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosArqTipoAlteracao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
}
