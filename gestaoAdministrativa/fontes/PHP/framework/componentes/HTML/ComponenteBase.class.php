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
* Servir de base para todos as classes de componente
* Data de Criação: 06/07/2006

* @author Desenvolvedor: Leandro André Zis

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

class ComponenteBase extends Objeto
{
    var $stRotulo;
    var $stTitle;
    var $boNull;
    var $boNullBarra;
    var $boObrigatorioBarra;
    var $stDefinicao;

    # Quando o Urbem estiver com PHP 5.4 esse deve ser o método utilizado, não será mais necessário o check encondig.
    # function setTitle($valor)            { $this->stTitle =  $valor; }
    
    public function setTitle($valor)            { $this->stTitle =  (strnatcmp(phpversion(),'5.4.0') < 0) ? (mb_check_encoding($valor, 'UTF-8') ? utf8_decode($valor) : $valor ) : $valor; }
    public function setRotulo($valor)           { $this->stRotulo = $valor; }
    public function setNull($valor)             { $this->boNull = $valor; }
    public function setNullBarra($valor)        { $this->boNullBarra = $valor; }
    public function setObrigatorio($valor)      { $this->boNull = !$valor;}
    public function setObrigatorioBarra($valor) { $this->boObrigatorioBarra = $valor; $this->boNullBarra = !$valor;}
    public function setDefinicao($valor)        { $this->stDefinicao  = $valor; }
    
    public function getTitle()            { return $this->stTitle; }
    public function getRotulo()           { return $this->stRotulo; }
    public function getNull()             { return $this->boNull; }
    public function getNullBarra()        { return $this->boNullBarra; }
    public function getObrigatorio()      { return !$this->boNull; }
    public function getObrigatorioBarra() { return $this->boObrigatorioBarra; }
    public function getDefinicao()        { return $this->stDefinicao; }

    public function ComponenteBase()
    {
        $this->setObrigatorio ( false  );
        $this->setObrigatorioBarra( false  );
    }

}

?>
