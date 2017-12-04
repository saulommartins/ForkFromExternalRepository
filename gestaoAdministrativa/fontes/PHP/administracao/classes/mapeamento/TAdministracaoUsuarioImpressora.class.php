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
* Classe de mapeamento para administracao.usuario_impressora
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  SW_USUARIO_IMPRESSORA
  * Data de Criação: 05/08/2004

  * @author Analista: Ricardo
  * @author Desenvolvedor: Cassiano Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoUsuarioImpressora extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoUsuarioImpressora()
{
    parent::Persistente();
    $this->setTabela( 'USUARIO_IMPRESSORA' );

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,cod_impressora');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('cod_impressora','integer',true,'',true,true);
    $this->AddCampo('impressora_padrao','boolean',true,'',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " select                                            \n";
    $stSql .= "     imp.*,                                        \n";
    $stSql .= "     usimp.impressora_padrao,                      \n";
    $stSql .= "     us.*                                          \n";
    $stSql .= " from                                              \n";
    $stSql .= "     administracao.impressora as imp,                        \n";
    $stSql .= "     administracao.usuario_impressora as usimp,              \n";
    $stSql .= "     administracao.usuario as us                             \n";
    $stSql .= " where                                             \n";
    $stSql .= "     imp.cod_impressora = usimp.cod_impressora and \n";
    $stSql .= "     us.numcgm = usimp.numcgm                   \n";
//    $stSql .= "     usimp.impressora_padrao = false               \n";
    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaImpressoraPadrao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaImpressoraPadrao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaRecuperaImpressoraPadrao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImpressoraPadrao()
{
    $stSql  = " select                                            \n";
    $stSql .= "     imp.*,                                        \n";
    $stSql .= "     usimp.impressora_padrao,                      \n";
    $stSql .= "     us.*                                          \n";
    $stSql .= " from                                              \n";
    $stSql .= "     administracao.impressora as imp,                        \n";
    $stSql .= "     administracao.usuario_impressora as usimp,              \n";
    $stSql .= "     administracao.usuario as us                             \n";
    $stSql .= " where                                             \n";
    $stSql .= "     imp.cod_impressora = usimp.cod_impressora and \n";
    $stSql .= "     us.numcgm = usimp.numcgm                  and \n";
    $stSql .= "     usimp.impressora_padrao = true                \n";

    return $stSql;
}

}
