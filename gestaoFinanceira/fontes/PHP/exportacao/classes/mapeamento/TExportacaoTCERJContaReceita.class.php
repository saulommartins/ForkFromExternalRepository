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
    * Classe de mapeamento da tabela ExportacaoTCERJ.CONTA_RECEITA
    * Data de Criação: 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-18 11:16:25 -0300 (Ter, 18 Jul 2006) $

    * Casos de uso: uc-02.08.09
*/

/*
$Log$
Revision 1.12  2006/07/18 14:16:25  cako
Bug #6589#

Revision 1.11  2006/07/18 13:20:45  cako
Bug #6589#

Revision 1.10  2006/07/05 20:45:58  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCERJContaReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoTCERJContaReceita()
{
    parent::Persistente();
    $this->setTabela('tcerj.conta_receita');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_conta,exercicio');

    $this->AddCampo('cod_conta','INTEGER',true,'',true,true);
    $this->AddCampo('exercicio','INTEGER',true,'',true,true);
    $this->AddCampo('cod_estrutural_tce','CHAR(08)',true,'',false,false);
    $this->AddCampo('lancamento','BOOLEAN',true,'',false,false);
}

function montaRecuperaDadosArqReceita()
{
    $stSQL  = " SELECT                                                          \n";
    $stSQL .= "   ocr.exercicio,                                                \n";
    $stSQL .= "   ocr.cod_conta,                                                \n";
//  $stSQL .= "   substr(replace(ocr.cod_estrutural,'.',''),1,8) as cod_estrutural, \n";
    $stSQL .= "   replace(ocr.cod_estrutural,'.','') as cod_estrutural,         \n";
    $stSQL .= "   ocr.descricao,                                                \n";
    $stSQL .= "   CASE WHEN tcr.cod_estrutural_tce is null                      \n";
    $stSQL .= "     THEN substr(replace(ocr.cod_estrutural,'.',''),1,8)         \n";
    $stSQL .= "     ELSE tcr.cod_estrutural_tce                                 \n";
    $stSQL .= "   END as cod_estrutural_tce,                                    \n";
    $stSQL .= "   tcr.lancamento                                                \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "   orcamento.conta_receita as ocr                                \n";
    $stSQL .= "   LEFT JOIN tcerj.conta_receita as tcr on(                      \n";
    $stSQL .= "       ocr.exercicio   = tcr.exercicio                           \n";
    $stSQL .= "   AND ocr.cod_conta   = tcr.cod_conta)                          \n";
    $stSQL .= " WHERE ocr.exercicio = '".$this->getDado("exercicio")."'         \n";
//  $stSQL .= "   AND length(replace(publico.fn_mascarareduzida(ocr.cod_estrutural),'.',''))<=8     \n";
    $stSQL .= " GROUP BY ocr.cod_estrutural,ocr.exercicio,ocr.cod_conta,        \n";
    $stSQL .= "          ocr.descricao,tcr.cod_estrutural_tce,tcr.lancamento    \n";
    $stSQL .= " ORDER BY ocr.cod_estrutural                                     \n";
//  echo $stSQL;
    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaArqReceita.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosArqReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosArqReceita().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
}
