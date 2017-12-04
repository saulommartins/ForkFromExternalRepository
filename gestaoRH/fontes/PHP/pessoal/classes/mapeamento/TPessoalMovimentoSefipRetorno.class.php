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
  * Classe de mapeamento da tabela PESSOAL.SEFIPRETORNO
  * Data de Criação: 16/02/2006

  * @author Analista: Vandre
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalMovimentoSefipRetorno extends Persistente
{
    /***
        * Método Construtor
        * @access Private
    */
    public function TPessoalMovimentoSefipRetorno()
    {
        parent::Persistente();
        $this->setTabela('pessoal.mov_sefip_retorno');
        $this->setCampoCod('cod_sefip_retorno');
        $this->setComplementoChave('');
        $this->AddCampo('cod_sefip_retorno','integer',true,'',true,"TPessoalSefip","cod_sefip");
    }

    /****
        * @access Public
        * @param  &$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao
        * @return obErro
    */

    public function recuperaRelacionamentoSefip(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if ( trim($stOrdem) != '' ) { $stOrdem = ' order by ' .$stOrdem;}

        $stSql  = $this->montaRecuperaRelacionamentoSefip ().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }// function recuperaRelacionamentoSefip(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") {

    /****
        * @access Public
        * @return obErro
    */
    public function montaRecuperaRelacionamentoSefip()
    {
        $stSql = " select sefip.* from pessoal.sefip
                   inner join pessoal.mov_sefip_retorno
                   on (sefip.cod_sefip = mov_sefip_retorno.cod_sefip_retorno) ";

        return $stSql;
    }//function montaRecuperaRelacionamentoSefip() {

    /**
        * Valida exclusão de sefip de retorno, verifica se tem ligação com a tabela mov_sefip_saida_mov_sefip_retorno
          se tiver não pode excluir
        * @access public
        * return obErro
    */
    public function validaExclusao($stFiltro = "", $boTransacao = "")
    {
        $obErro       = new erro;
        $obConexao    = new Conexao;
        $rsSefipSaida = new RecordSet;

        $stSql .= $this->montaValidaExclusao();
        $stSql .= $this->getDado('cod_sefip_retorno');

        $obErro       = $obConexao->executaSQL( $rsSefipSaida, $stSql, $boTransacao );

        if ( !$obErro->ocorreu()) {
            if ( $rsSefipSaida->getNumLinhas() > 0 ) {
                $obErro->setDescricao ('Esta Sefip está sendo utilizada por uma Sefip de Afastamento, por esse motivo não pode ser excluida!');
            }
        }

        return $obErro;
    }//function validaExclusao() {

    public function montaValidaExclusao()
    {
       $stSql = '';
       $stSql  = 'SELECT      *                                         ';
       $stSql .= ' FROM       pessoal.mov_sefip_saida_mov_sefip_retorno ';
       $stSql .= ' where mov_sefip_saida_mov_sefip_retorno.cod_sefip_retorno = ';

       return $stSql;
    }// function montaValidaExclusao() {

}//class TPessoalMovimentoSefipRetorno extends Persistente{
