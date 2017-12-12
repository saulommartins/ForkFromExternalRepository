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
  * Classe de mapeamento da tabela PESSOAL.MOV_SEFIP_SAIDA_MOV_SEFIP_RETORNO
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30936 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso :uc-04.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.MOV_SEFIP_SAIDA_MOV_SEFIP_RETORNO
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalMovSefipSaidaMovSefipRetorno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalMovSefipSaidaMovSefipRetorno()
{
    parent::Persistente();
    $this->setTabela('pessoal.mov_sefip_saida_mov_sefip_retorno');

    $this->setCampoCod('cod_sefip_saida');
    $this->setComplementoChave('');

    $this->AddCampo('cod_sefip_saida','integer',true,'',true,true);
    $this->AddCampo('cod_sefip_retorno','integer',true,'',false,"TPessoalMovimentoSefipRetorno","cod_sefip_retorno");

}

function montaRecuperaRelacionamento()
{
    $stSQL .=" SELECT                                                                                      \n";
    $stSQL .="        cod_sefip_saida,                                                                     \n";
    $stSQL .="        cod_sefip,                                                                           \n";
    $stSQL .="        descricao,                                                                           \n";
    $stSQL .="        trim(num_sefip) as num_sefip                                                         \n";
    $stSQL .="   FROM                                                                                      \n";
    $stSQL .="       pessoal.mov_sefip_saida mss,                                                      \n";
    $stSQL .="       pessoal.sefip           ps                                                        \n";
    $stSQL .="   WHERE                                                                                     \n";
    $stSQL .="       mss.cod_sefip_saida NOT IN(SELECT msr.cod_sefip_saida                                 \n";
    $stSQL .="                                    FROM pessoal.mov_sefip_saida_mov_sefip_retorno msr)  \n";
    $stSQL .="     AND  mss.cod_sefip_saida = ps.cod_sefip                                                 \n";

    return $stSQL;
}

/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da inclusão.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true
*/
function validaInclusao($stFiltro = "" , $boTransacao = "")
{
    $obErro    = new Erro;
    $obConexao = new Conexao;
    $stSql     =  'select * from pessoal.mov_sefip_retorno where cod_sefip_retorno = '.$this->getDado('cod_sefip_retorno');
    $this->setDebug ( $stSql );
    $obErro       = $obConexao->executaSQL ( $rsRecordSet, $stSql, $boTransacao );
    if (!$obErro->ocorreu()) {
        if ( $rsRecordSet->getNumLinhas() <= 0 ) {
            Sessao::getExcecao()->setDescricao ('O código  de Sefip para retorno é inválido');
        }
    }

    return $obErro;
}

function recuperaMovSefipRetorno(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY num_sefip";
    $stSql = $this->montaRecuperaMovSefipRetorno().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMovSefipRetorno()
{
    $stSQL .=" SELECT sefip.*                                                                              \n";
    $stSQL .="      , cod_sefip_retorno                                                                    \n";
    $stSQL .="   FROM pessoal.mov_sefip_saida_mov_sefip_retorno                                            \n";
    $stSQL .="      , pessoal.sefip                                                                        \n";
    $stSQL .="  WHERE mov_sefip_saida_mov_sefip_retorno.cod_sefip_retorno = sefip.cod_sefip                \n";

    return $stSQL;
}

}
