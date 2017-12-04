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
    * Classe de regra de negócio PessoalMovimentoSefipSaida
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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentoSefip.class.php"                               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoria.class.php"                                    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCategoriaMovimento.class.php"                        );

class RPessoalCategoriaMovimento
{
    /**
    * @access Private
    * @var Integer
    */

    public $stIndicativo;

    /**
    * @access Private
    * @var Integer
    */
    public $obRORPessoalMovimentoSefipSaida;

    /**
        * @access Private
        * @var Objeto
    */
    public $obRORPessoalCategoria;

    /**
        * @access Public
        * @param String $valor
    */
    public function setIndicativo($valor) { $this->stIndicativo = $valor; }

    /**
        * @access Public
        * @return String
    */
    public function getIndicativo() { return $this->stIndicativo; }

    /**
        * @access Public
        * @param Object valor
    */
    public function setobRPessoalMovimentoSefipSaida($valor) { $this->obRORPessoalMovimentoSefipSaida = &$valor; }

    /**
        * @access Public
        * @return Object
    */
    public function getobRPessoalMovimentoSefipSaida() { return $this->obRORPessoalMovimentoSefipSaida; }

    /**
        * @access Public
        * @param Objeto
    */
    public function setobRPessoalCategoria(&$Objeto) { $this->obRORPessoalCategoria = $Objeto; }

    /**
        * @access Public
        * @return Objeto
    */
    public function getobRPessoalCagegoria() { return $this->obRORPessoalCategoria;   }

    /**
        * Método construtor
        * @access Publico
    */
    public function RPessoalCategoriaMovimento($obRPessoalMovimentoSefipSaida)
    {
            $this->setobRPessoalMovimentoSefipSaida( $obRPessoalMovimentoSefipSaida );
            $this->setobRPessoalCategoria          ( new RPessoalCategoria          );
    }

    /**
        * salva a categoria de movimento
        * @access publico
        * @param transacao:boolean
        * @return erro
    */
    public function incluirCategoriaMovimento($boTransacao = '')
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCategoriaMovimento.class.php");

        $obTCategoriaMovimento = new TPessoalCategoriaMovimento;

        $obTCategoriaMovimento->setDado ( "cod_sefip_saida", $this->obRORPessoalMovimentoSefipSaida->getCodSefip() );
        $obTCategoriaMovimento->setDado ( "cod_categoria",   $this->obRORPessoalCategoria->getCodCategoria()       );
        $obTCategoriaMovimento->setDado ( "indicativo",      $this->getIndicativo()                                );
        $obErro =  $obTCategoriaMovimento->inclusao($boTransacao);

        return $obErro;

    }//function incluirCategoriaMoviemento($transacao = '') {

    /**
        * exclui categoria de movimento
        * @access publico
        * @param transacao
        * @return erro
    */
    public function excluirCategoriaMovimento()
    {
    }// function excluirCategoriaMovimento() {

}// class RPessoalCategoriaMovimento
