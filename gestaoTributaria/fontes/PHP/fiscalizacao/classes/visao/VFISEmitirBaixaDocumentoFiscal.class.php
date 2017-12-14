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
 * Classe de visão para emitir BAIXA DE DOCUMENTOS FISCAIS
 * Data de Criação:12/02/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 * @author Janilson Mendes P. da Silva <janilson.silva@cnm.org.br>
 *
 * @package URBEM
 * @subpackage Visao
 *
 * $Id: $
 *
 * Casos de uso:
 */

require_once( CAM_GT_FIS_NEGOCIO . 'RFISEmitirBaixaDocumentoFiscal.class.php' );

class VFISEmitirBaixaDocumentoFiscal
{

    /**
     * Objeto negócio
     */
    private $obRegra;

    /**
    * Método construtor
    * @return void
    */
    public function __construct()
    {
        $this->obRegra = new RFISEmitirBaixaDocumentoFiscal;
    }

    /**
     * Método para emitir BAIXA DE DOCUMENTOS FISCAIS
     *
     * @param  array $arParametros
     * @return void
     */
    public function emitirBaixaDocFiscal(array $arParametros)
    {
        $this->obRegra->emitirBaixaDocFiscal($arParametros);
    }

}

?>
