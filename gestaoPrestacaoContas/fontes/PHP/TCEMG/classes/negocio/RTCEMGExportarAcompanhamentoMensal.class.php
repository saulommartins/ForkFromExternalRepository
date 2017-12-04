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
 * Classe de regra de exportacao dos arquivos de planejamento TCE/MG
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Carlos Adriano   <carlos.silva@cnm.org.br>
 * $Id: RTCEMGExportarAcompanhamentoMensal.class.php 57109 2014-02-04 16:30:30Z michel$
 */

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

/**
    * Classe de Regra para geração de arquivos de acompanhamento para o ExportacaoTCE-MG

    * @author   Desenvolvedor :  Carlos Adriano
*/
class RTCEMGExportarAcompanhamentoMensal
{
    /* Valores entre*/
    public $stCodEntidades ;
    public $stExercicio    ;
    public $stMes;
    public $arArquivos = array();
    public $stDataInicial;
    public $stDataFinal;

    /**
    * Metodo Construtor
    * @access Private
    */
    public function RTCEMGExportarAcompanhamentoMensal()
    {
    }

    // SETANDO
    public function setCodEntidades($valor) {   $this->stCodEntidades   =   $valor; }
    public function setExercicio($valor) {   $this->stExercicio      =   $valor; }
    public function setMes($valor) {   $this->stMes            =   $valor; }
    public function setArquivos($valor) {   $this->arArquivos       =   $valor; }
    public function setDataInicial($valor) {   $this->stDataInicial    =   $valor; }
    public function setDataFinal($valor) {   $this->stDataFinal      =   $valor; }

    // GETANDO
    public function getCodEntidades() { return $this->stCodEntidades ; }
    public function getExercicio() { return $this->stExercicio    ; }
    public function getMes() { return $this->stMes          ; }
    public function getArquivos() { return $this->arArquivos     ; }
    public function getDataInicial() { return  $this->stDataInicial ; }
    public function getDataFinal() { return $this->stDataFinal    ; }

    // Gerando Recordset
    public function geraRecordset(&$arRecordSetArquivos)
    
    {
            
        $obErro = new Erro;

        return $obErro;
    }
}
?>
