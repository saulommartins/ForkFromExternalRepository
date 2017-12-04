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
    * Classe de regra de negócio PessoalMovimentoSefipRetorno
    * Data de Criação: 04/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso :uc-04.04.40
*/

include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSefip.class.php" );

class RPessoalMovimentoSefipRetorno extends RPessoalSefip
{
    /***
        * @access private
        * @var objeto
    */

    public $obTPessoalMovimentoSefipSaida;

    /****
        * @access private
        * @var obTransacao
    */
    public $obTransacao;
    /***

        * @access public
        * @param objeto
    */
    public function setobTPessoalMovimentoSefipSaida($Objeto = '') { $this->obTPessoalMovimentoSefipSaida = $Objeto; }

    /***
        * @access
        * @retorno objeto
    */
    public function getobTPessoalMovimentoSefipSaida() { return  obTPessoalMovimentoSefipSaida; }

    /***
        * @metodo contrutor
        * @access public
        * @param objeto
    */
    public function RPessoalMovimentoSefipRetorno($Objeto = '')
    {
        parent::RPessoalSefip();
        $this->setobTPessoalMovimentoSefipSaida( $Objeto );
        $this->obTransacao = new Transacao;
    }// function RPessoalMovimentoSefipRetorno ( &$Objeto )

    /***
        * @inclusão de sefip de retorno
        * @access public
        * @param transacao
        * @retorno erro
    */
    public function incluirMovimentoSefipRetorno($transacao = '')
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php" );

        $obErro = $this->incluirSefip ( transacao ); // tem que verificar se terá que ser feito a metodo
                                             // inlcuir que consta no diagrama já que o ojeto SEFIP
                                             // só tem metodo salvar
                                             // resp.: foi criado o método incluir para não ser necessário alterar a função
                                             // salvar, a inluir apenas verifica se o Num Sefip informado já existe
        if (!$obErro->ocorreu()) {
            $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno;

            $obTPessoalMovimentoSefipRetorno->setDado ( "cod_sefip_retorno",  $this->getCodSefip() );
            $obTPessoalMovimentoSefipRetorno->inclusao( transacao                                  );

        }// if (!$obErro->ocorreu())

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTPessoalMoviemtnoRetornoSefip);

        return $obErro;

    }//function incluirMovimentoSefipRetorno ( transacao = '' )

    /***
        * @exclusao de sefip de retorno
        * @access public
        * @param transacao
        * @retorno erro
    */
    public function excluirMovimentoSefipRetorno($boTransacao = '')
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php" );
        $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno;
        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTPessoalMovimentoSefipRetorno->setDado ('cod_sefip_retorno', $this->getCodSefip());

            $obErro = $obTPessoalMovimentoSefipRetorno->exclusao( $boTransacao );

            if (!$obErro->ocorreu()) {
                // chamando exclusão da SuperClasse

                $obErro =$this->excluir($boTransacao );

            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalMovimentoSefipRetorno );

        return $obErro;
    }// function excluirMovimentoSefipRetorno($transacao = '') {

    public function listarMovimentoSefipRetorno(&$rsRecordSet, $boTransacao = "")
    {
         include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php" );

         $stFiltro = '';

         if ( $this->getCodSefip()           ) { $stFiltro .= " and sefip.cod_sefip  =" . $this->getCodSefip ()                   ; }
         if ( $this->getDescricao()          ) { $stFiltro .= " and upper(sefip.descricao) like upper ('". $this->getDescricao()."%')"  ; }
         if ( $this->getNumSefip()           ) { $stFiltro .= " and upper(sefip.num_sefip) like upper ('". $this->getNumSefip() ."%')"  ; }
         if ( $this->getRepetirMensalmente() ) { $stFiltro .= " and sefip.repetir_mensal = '". $this->getRepetirMensalmente()."'" ; }

         if ( strtoupper(substr($stFiltro,0,4)) == ' AND ') {
             $stFiltro = ' WHERE '.substr($stFiltro,4);
         }
         $stOrder = ' sefip.descricao ';

         $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno;
         $obErro = $obTPessoalMovimentoSefipRetorno->recuperaRelacionamentoSefip ($rsRecordSet ,
                                                                                  $stFiltro ,
                                                                                  $stOrder  ,
                                                                                  $boTransacao );

         return $obErro;
    }// function listarMovimentoSefipRetorno(&$rsRecordSet, $boTransacao = "") {

    /**
        executa um busca usando o método listar e se achar um registro prenche
        as propriedades da classe com seus respectivos campos a busca é feita por código (chave primária)
        e portando recupera apenas um registro se nenhum.
        Se nenhum registro for encontrado as propriedades ficaram nulas e retorna erro
    */

    public function consultar($boTransacao = '')
    {
         /*

         */
         include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php" );
         $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno;
         $obErro = new erro;

         if ( $this->getNumSefip() ) {
             $stFiltro =    "where trim(upper(sefip.num_sefip)) = trim(upper ('". $this->getNumSefip()."'))"  ;
         } elseif ( $this->getCodSefip() ) {
             $stFiltro = 'where cod_sefip = '.$this->getCodSefip();
         } else {
             $obErro->setDescricao('Valor de sefip de retorno inválido!');
         }

         if ( !$obErro->ocorreu() ) {

             $obErro = $obTPessoalMovimentoSefipRetorno->recuperaRelacionamentoSefip ( $rsRecordSet,
                                                                             $stFiltro,
                                                                             $stOrder,
                                                                             $boTransacao );
            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                 $this->setCodSefip           ( $rsRecordSet->getCampo ('cod_sefip')           );
                 $this->setDescricao          ( $rsRecordSet->getCampo ('descricao')           );
                 $this->setRepetirMensalmente ( $rsRecordSet->getCampo ('repetir_mensalmente') );
            } else {
                 $this->setCodSefip           ( '' );
                 $this->setDescricao          ( '' );
                 $this->setRepetirMensalmente ( '' );
            }
        }

        return $obErro;
    }// function consultar

}//class RPessoalMovimentoSefipRetorno extends RPessoalSefip{
