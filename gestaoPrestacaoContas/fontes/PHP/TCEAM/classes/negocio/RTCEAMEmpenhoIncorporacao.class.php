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
/*
 * Classe de negócio do Tipo de Documento
 *
 * @package SW2
 * @subpackage Negocio
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMEmpenhoIncorporacao.class.php";

class RTCEAMEmpenhoIncorporacao
{
    /*
        * @var Object
        * @access Private
    */
    public $obTTCEAMEmpenhoIncorporacao;

    public function RTCEAMEmpenhoIncorporacao()
    {
        $this->obTTCEAMEmpenhoIncorporacao = new TTCEAMEmpenhoIncorporacao();
    }

    public function incorporarEmpenhos($stCodEntidades, $boTransacao = "")
    {
        $obErro = new Erro;
        $rsElementos = new RecordSet;

        $this->obTTCEAMEmpenhoIncorporacao->setDado('exercicio', Sessao::getExercicio());
        $this->obTTCEAMEmpenhoIncorporacao->setDado('stCodEntidades', $stCodEntidades);
        $this->obTTCEAMEmpenhoIncorporacao->recuperaElementos($rsElementos, $boTransacao);

        foreach ($rsElementos->arElementos as $arElemento) {
            $this->obTTCEAMEmpenhoIncorporacao->setDado('exercicio', $arElemento['exercicio']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('cod_entidade', $arElemento['cod_entidade']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('categoria_economica', $arElemento['categoria_economica']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('natureza', $arElemento['natureza']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('modalidade', $arElemento['modalidade']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('elemento', $arElemento['elemento']);
            $this->obTTCEAMEmpenhoIncorporacao->setDado('cod_recurso', $arElemento['cod_recurso']);
            $obErro = $this->obTTCEAMEmpenhoIncorporacao->incorporarEmpenhos($boTransacao);
        }

        return $obErro;
    }

    public function deletarIncorporacao($boTransacao = "")
    {
        $this->obTTCEAMEmpenhoIncorporacao->setDado('exercicio', Sessao::getExercicio());
        $this->obTTCEAMEmpenhoIncorporacao->excluirIncorporarEmpenhos($boTransacao);
    }
}
