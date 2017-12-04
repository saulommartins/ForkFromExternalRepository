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
* Classe para trabalhar com as linhas do arquivo
* Data de Criação: 13/01/2005

* @author Analista/Desenvolvedor: Diego Barbosa Victoria

* @package Framework
* @subpackage Arquivos

$Revision: 3473 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:06:34 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.01.00

*/

/**
* Classe para trabalhar com as linhas do arquivo
* @author Analista/Desenvolvedor: Diego Barbosa Victoria
* @package Framework
* @subpackage Arquivos
*/
class LinhaArquivo
{
    /**
    * @access Private
    * @var String
    */
    public $stConteudo;

    /**
    * @access Private
    * @var Object
    */
    public $roArquivo;

    /**
    * @access Public
    * @param String $valor
    */
    public function setConteudo($valor) { $this->stConteudo   = $valor; }

    /**
    * @access Public
    * @Return String
    */
    public function getConteudo() { return $this->stConteudo;   }

    /**
    * Método Construtor
    * @access Private
    */
    public function LinhaArquivo(&$roArquivo)
    {
        $this->roArquivo = &$roArquivo;
    }
}
?>
