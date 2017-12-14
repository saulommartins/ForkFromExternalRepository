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
    * Classe de Exportação Arquivos Principais

    * Data de Criação   : 04/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Exportador

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-01-15 09:33:26 -0200 (Seg, 15 Jan 2007) $

    * Casos de uso: uc-02.08.01   ???????????????????????
*/

/*
$Log$
Revision 1.1  2007/01/15 11:33:26  bruce
*** empty log message ***

Revision 1.5  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

class RExportacaoTCEPBArquivosPrincipais
{
    /* Valores entre*/
    public $inPeriodo      ;
    public $stCodEntidades ;
    public $inCodOrgao     ;
    public $stDataInicial  ;
    public $stDataFinal    ;
    public $stExercicio    ;
    public $arArquivos = array()     ;

    public function RExportacaoTCEPBArquivosPrincipais()
    {
    }

    // SETANDO
    public function setPeriodo($valor) {   $this->inPeriodo        =   $valor; }
    public function setCodEntidades($valor) {   $this->stCodEntidades   =   $valor; }
    public function setExercicio($valor) {   $this->stExercicio      =   $valor; }
    public function setCodOrgao($valor) {   $this->inCodOrgao       =   $valor; }
    public function setArquivos($valor) {   $this->arArquivos       =   $valor; }
    public function setDataInicial($valor) {   $this->stDataInicial    =   $valor; }
    public function setDataFinal($valor) {   $this->stDataFinal      =   $valor; }

    // GETANDO
    public function getPeriodo() {   return $this->inPeriodo     ;   }
    public function getCodEntidades() {   return $this->stCodEntidades;   }
    public function getExercicio() {   return $this->stExercicio   ;   }
    public function getCodOrgao() {   return $this->inCodOrgao    ;   }
    public function getArquivos() {   return $this->arArquivos    ;   }
    public function getDataInicial() {   return $this->stDataInicial ;   }
    public function getDataFinal() {   return $this->stDataFinal   ;   }

    public function geraRecordset(&$arRecordset)
    {
        if ( in_array ( 'LIQUIDAC.TXT', $this->getArquivos()) ) {

        }

    }

}
