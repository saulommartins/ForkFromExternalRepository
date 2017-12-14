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
* Classe de agrupamentos de objetos para o Filtro por Contrato
* Data de Criação: 07/03/2008

* @author Analista: Dagiane Oliveira
* @author Desenvolvedor: Alex Cardoso

* @package framework
* @subpackage componentes

$Id: IFiltroCGMServidorDependente.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IBuscaInnerCGMServidorDependente.class.php" );

class IFiltroCGMServidorDependente extends Objeto
{
    /**
        * @access Private
        * @var String
    */
    public $stTituloFormulario;
    /**
        * @access Private
        * @var Boolean
    */
    public $obBscDependente;
    /**
        * @access Private
        * @var Boolean
    */
    public $obCmbContrato;

    /**
        * @access Public
        * @param String $Valor
    */
    public function setTituloFormulario($valor) { $this->stTituloFormulario = $valor; }

    /**
        * @access Public
        * @return String
        * Tipos:
    */
    public function getTituloFormulario() { return $this->stTituloFormulario; }

    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroCGMServidorDependente($boFiltrarPensaoJudicial=false)
    {
        $this->obBscDependente = new IBuscaInnerCGMServidorDependente($boFiltrarPensaoJudicial);
        $this->obBscDependente->setPreencheCombo(true);

        $this->obCmbContrato = new Select;
        $this->obCmbContrato->setRotulo                   ( "Matrícula"                               );
        $this->obCmbContrato->setTitle                    ( "Informe a matrícula do CGM selecionado." );
        $this->obCmbContrato->setName                     ( "inContrato"                              );
        $this->obCmbContrato->setId                       ( "inContrato"                              );
        $this->obCmbContrato->setValue                    ( ""                                        );
        $this->obCmbContrato->setStyle                    ( "width: 200px"                            );
        $this->obCmbContrato->addOption                   ( "", "Selecione"                           );

        $this->setTituloFormulario("Filtro por CGM/Matrícula");
    }

    /**
        * Monta os combos de localização conforme o nível setado
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addTitulo                    ( $this->getTituloFormulario()                    );
        $this->obBscDependente->geraFormulario($obFormulario);
        $obFormulario->addComponente                ( $this->obCmbContrato                            );
    }

}
?>
