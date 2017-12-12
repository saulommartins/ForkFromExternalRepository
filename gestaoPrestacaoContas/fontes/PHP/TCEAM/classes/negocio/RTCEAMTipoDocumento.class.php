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

class RTCEAMTipoDocumento
{

//    /**
//        * @var String
//        * @access Private
//    */
//    var $stExercicio;
//
//    /**
//         * @access Public
//         * @param String $valor
//    */
//    function setExercicio($valor) { $this->stExercicio = $valor; }
//
//    /**
//         * @access Public
//         * @param String $valor
//    */
//    function getExercicio() { return $this->stExercicio; }
//
//    /**
//        * Método Construtor
//        * @access Private
//    */
//    function RTCEAMTipoDocumento()
//    {
//        $this->setExercicio(Sessao::getExercicio());
//    }

    /**
        * Lista todos os tipos de documentos de acordo com o filtro
        * @access Public
        * @param  Object $rsLista Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
        * @author eduardo.schitz@cnm.org.br&gt;
    */
    public function recuperaTipoDocumento(&$rsLista, $stOrder = 'cod_tipo', $obTransacao = '')
    {
        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumento.class.php';
        $obTTCEAMTipoDocumento = new TTCEAMTipoDocumento;

        $obErro = $obTTCEAMTipoDocumento->recuperaTodos($rsLista, '', $stOrder, $obTransacao);

        return $obErro;
    }

}
