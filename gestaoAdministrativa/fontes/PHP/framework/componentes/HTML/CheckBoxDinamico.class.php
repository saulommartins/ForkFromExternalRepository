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
* Gerar um ou mais checkbox's com base no recordSet definido pelo usuário da classe
*
* Data de Criação: 22/08/2006


* @author Desenvolvedor: Bruce Cruz de Sena
* @author Documentor:

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

class CheckBoxDinamico extends Componente
{
    public $stTitulo       = ''     ;
    public $stRotulo       = ''     ;
    public $arComponentes  = array();
    public $stCampoTitulo  = ''     ;
    public $stCampoNome    = ''     ;
    public $stCampoLabel   = ''     ;
    public $stEvento       = ''     ;
    public $stPrefixoNome  = ''     ;
    public $stPrefixoLabel = ''     ;
    public $rsRecordSet;

    // set's
    public function setTitulo($stTitulo   = '') { $this->stTitulo       = $stTitulo   ; }
    public function setRotulo($rotulo     = '') { $this->stRotulo       = $rotulo     ; }
    public function setCampoNome($nome       = '') { $this->stCampoNome    = $nome       ; }
    public function setCampotitulo($titulo     = '') { $this->stCampoTitulo  = $titulo     ; }
    public function setCampoLabel($label      = '') { $this->stCampoLabel   = $label      ; }
    public function setEvento($evento     = '') { $this->stEvento       = $evento     ; }
    public function setRecordSet(&$recordset) { $this->rsRecordSet    = &$recordset ; }
    public function setArrayComponentes ( $array = array() ) { $this->arComponentes  = $array      ; }
    public function setPrefixoLabel($prefixo = '') { $this->stPrefixoLabel = $prefixo    ; }
    public function setPrefixoNome($prefixo = '') { $this->stPrefixoNome  = $prefixo    ; }

    // get's
    public function getTitulo() { return $this->stTitulo       ; }
    public function getRotulo() { return $this->stRotulo       ; }
    public function getCampoNome() { return $this->stCampoNome    ; }
    public function getCampoTitulo() { return $this->stCampoTitulo  ; }
    public function getCampoLabel() { return $this->stCampoLabel   ; }
    public function getEvento() { return $this->stEvento       ; }
    public function getPrefixoLabel() { return $this->stPrefixoLabel ; }
    public function getPrefixoNome() { return $this->stPrefixoNome  ; }

    public function geraFormulario(&$obFormulario)
    {
            if ($this->rsRecordSet != null) {
                 $i = 0;
                 $arChecks = array();

                 while ( !$this->rsRecordSet->eof() ) {

                    $arChecks[$i]['name']   = trim($this->getPrefixoNome()) .str_replace(" ","",$this->rsRecordSet->getCampo ( $this->getCampoNome())   ) ;
                    //$arChecks[$i]['label']  = $this->getPrefixoLabel()      .str_replace(" ","",$this->rsRecordSet->getCampo ( $this->getCampoLabel())  ) ;
                    $arChecks[$i]['label']  = $this->getPrefixoLabel()      .$this->rsRecordSet->getCampo ( $this->getCampoLabel());

                    if ( $this->getCampoTitulo() ) {
                        $arChecks[$i]['Title']  = $this->rsRecordSet->getCampo ( $this->getCampoTitulo() );
                    } elseif ($this->getTitulo() ) {
                        $arChecks[$i]['Title']  = $this->getTitulo();
                    }

                    $arChecks[$i]['value']  = false ;
                    $arChecks[$i]['evento'] = $this->getEvento();
                    $arChecks[$i]['rotulo'] = $this->getRotulo();
                    $i++;
                    $this->rsRecordSet->proximo();
                 }

                 $this->arComponentes = array();
                 foreach ($arChecks as $linha) {
                        $this->arComponentes[] = $this->criaCheckBox($linha['name'], $linha['rotulo'] , $linha['label'],  $linha['Title'], $linha['value'] );
                 }

            }

            foreach ($this->arComponentes as $componente) {
                $obFormulario->addComponente( $componente );
            }
    }

    public function criaCheckBox($nome, $rotulo, $label, $titulo, $value)
    {
         $retChk = new CheckBox;
         $retChk->setName    ( $nome   );
         $retChk->setId      ( $nome   );
         $retChk->setRotulo  ( $rotulo ); //texto que vai a esquerda do Check Box e só aparece na primeira linha
         $retChk->setLabel   ( $label  ); //texto que aparece ao lado direito do componente ou nome da opção
         $retChk->setTitle   ( $titulo ); //texto que aparece como dica qdo o user passa o mouse pelo componente
         $retChk->setChecked ( $value  );

         return $retChk;
    }

}

?>
