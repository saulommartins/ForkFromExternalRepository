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
    * Classe do componente Tipo de Norma
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.04.02
*/

/*
$Log$
Revision 1.3  2006/10/17 18:18:15  vandre
Complementaçao.

Revision 1.2  2006/10/17 13:25:24  vandre
Adicionada opção pra setar como obrigatoriedade do campo tipo norma.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php"                                        );

class ITextBoxSelectTipoNorma extends TextBoxSelect
{
/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Private
    * @var Objeto
*/
var $inCodTipoNorma;
/**
    * @access Private
    * @var Objeto
*/
var $obRTipoNorma;
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRTipoNorma($valor) { $this->obRTipoNorma         = $valor; }
/**
    * @access Public
    * @return Objeto
*/

function setNull($valor)
{
    $this->boNull                                  = $valor;
}
/**
    * @access Public
    * @return Objeto
*/

function getRTipoNorma() { return $this->obRTipoNorma; }
/**
    * Método construtor
    * @access Private

*/
function getNull() { return $this->boNull; }
/**
    * Método construtor
    * @access Private
*/
function ITextBoxSelectTipoNorma($boNull=false)
    {
        parent::TextBoxSelect();

        $this->setNull($boNull);

        $obRTipoNorma           = new RTipoNorma;
        $obRTipoNorma->listar($rsRecordSet,"","");

        $this->setRotulo              ( "Tipo de Norma"                 );
        $this->setName                ( "inCodTipoNorma"                );
        $this->setTitle               ( "Selecione o tipo da norma."    );
        $this->setNull                ($this->getNull()                 );

        $this->obTextBox->setRotulo              ( "Tipo de Norma"             );
        $this->obTextBox->setTitle               ( "Selecione o tipo da norma.");
        $this->obTextBox->setName                ( "inCodTipoNormaTxt"         );
        $this->obTextBox->setId                  ( "inCodTipoNormaTxt"         );
        $this->obTextBox->setSize                ( 11                          );
        $this->obTextBox->setMaxLength           ( 11                          );
        $this->obTextBox->setInteiro             ( true                        );

        $this->obSelect->setRotulo              ( "Tipo de Norma"                 );
        $this->obSelect->setName                ( "inCodTipoNorma"                );
        $this->obSelect->setId                  ( "inCodTipoNorma"                );
        $this->obSelect->setCampoID             ( "cod_tipo_norma"                );
        $this->obSelect->setCampoDesc           ( "nom_tipo_norma"                );
        $this->obSelect->addOption              ( "", "Selecione"                 );
        $this->obSelect->preencheCombo          ( $rsRecordSet                    );
        $this->obSelect->setStyle               ( "width: 200px"                  );

}

function setTipoNorma($inCodTipoNorma)
{
    $this->inCodTipoNorma = $inCodTipoNorma;
}
/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
 function montaHTML()
 {
        if ($this->inCodTipoNorma != "") {
           $this->obTextBox->setValue($this->inCodTipoNorma);
           $this->obSelect->setValue($this->inCodTipoNorma);
        }
        parent::montaHTML();
    }
}
?>
