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
 * Classe de visão para emitir Termo.
 * Data de Criação: 11/11/2008
 *
 *
 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Marcio Medeiros
 *
 * @package URBEM
 * @subpackage Visao
 *
 * $Id: VFISEmitirTermo.class.php 59612 2014-09-02 12:00:51Z gelson $
 *
 * Casos de uso:
 */
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once( CAM_GT_FIS_NEGOCIO . 'RFISEmitirTermo.class.php' );
include_once( CAM_GT_FIS_NEGOCIO . "RFISNotificarProcesso.class.php" );
include_once( CAM_GT_FIS_VISAO   . "VFISNotificarProcesso.class.php" );

class VFISEmitirTermo
{
    /**
     *
     * @var object
     */
    private $obController;

    /**
     * Classe Model
     *
     * @var object
     */
    private $obNegocio;

    /**
     * Visão de NotificarProcesso
     *
     * @var object
     */
    private $obVFISNotificarProcesso;

    /**
     * Método construtor
     * @arParametros $obController objeto da regra de negócio
     */
    public function __construct($obController)
    {
        $this->obController = $obController;
        $this->obNegocio = new RFISEmitirTermo;
        $this->obVFISNotificarProcesso = new VFISNotificarProcesso(new RFISNotificarProcesso);
    }

     /**
      * Executa ação recebida na página de processamento (PR).
      *
      * @param array $arParametros
      * @return void
     */
    public function executarAcao(array $arParametros)
    {
        //Sessao::setTrataExcecao( true );

        $stMetodo = $arParametros['stAcao'];
        $this->inCodProcesso = $arParametros['inCodProcesso'];

        if ( is_string( $stMetodo ) ) {
            $this->$stMetodo( $arParametros );
        }

        //Sessao::encerraExcecao();
    }

    /**
    * Salva uma Receita e seus respectivos recursos.
    *
    * @param array $arParam
    * @return void
    */
    public function incluir(array $arParametros)
    {
        return $this->obNegocio->incluir($arParametros);
    }

    /**
     * Encapsula o método limparInfracao de VFISNotificarProcesso
     *
     * @return bool
     */
    public function limparInfracao()
    {
        return $this->obVFISNotificarProcesso->limparInfracao($_REQUEST);
    }

    /**
     * Encapsula o método alterarInfracao de VFISNotificarProcesso
     *
     * @return bool
     */
    public function alterarInfracao()
    {
        return $this->obVFISNotificarProcesso->alterarInfracao($_REQUEST);
    }

    /**
     * Encapsula o método incluirInfracao de VFISNotificarProcesso
     *
     * @return bool
     */
    public function incluirInfracao()
    {
        return $this->obVFISNotificarProcesso->incluirInfracao($_REQUEST);
    }

    /**
     * Encapsula o método excluirInfracao de VFISNotificarProcesso
     *
     * @return bool
     */
    public function excluirInfracao()
    {
        return $this->obVFISNotificarProcesso->excluirInfracao($_REQUEST);
    }

}
?>
