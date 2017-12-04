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
*
* Data de Criação: 10/08/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Aldo Jean Soares Silva

* @package framework
* @subpackage componentes

*/
include_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAMontaVeiculoPublicitario.class.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaVeiculoPublicitario/JSMontaVeiculoPublicitario.js';

class MontaVeiculoPublicitario extends Objeto
{
    public $obForm;
    public $obTxtCodTpVeiculoPublicitario;
    public $obCmbTpVeiculoPublicitario;
    public $obTxtCodEmpresa;
    public $obCmbEmpresa;
    public $obSpnVeiculoPublicitario;

    /**
    * Monta os combos de serviço conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
    */

    public function MontaVeiculoPublicitario()
    {

        $this->obForm = $obForm;
        $this->obSpnVeiculoPublicitario = new Span;
        $this->obSpnVeiculoPublicitario->setID("spnVeiculoPublicitario");

        $rsTipoVeiculoPublicitario = new RecordSet;
        $obRPPAMontaVeiculoPublicitario = new VPPAMontaVeiculoPublicitario;
        $obRPPAMontaVeiculoPublicitario->listarTiposVeiculoPublicitario( $rsTipoVeiculoPublicitario );

        $rsEmpresas = new RecordSet;

        $this->obTxtCodTpVeiculoPublicitario = new TextBox;
        $this->obTxtCodTpVeiculoPublicitario->setRotulo             ( "Veículo Publicitário"                );
        $this->obTxtCodTpVeiculoPublicitario->setName               ( "inCodigoTipoVeiculoPublicitario"            );
        $this->obTxtCodTpVeiculoPublicitario->setValue              ( $inCodigoTpVeiculoPublicitario             );
        $this->obTxtCodTpVeiculoPublicitario->setSize               ( 8                       );
        $this->obTxtCodTpVeiculoPublicitario->setMaxLength          ( 8                       );
        $this->obTxtCodTpVeiculoPublicitario->setNull               ( false                   );
        $this->obTxtCodTpVeiculoPublicitario->obEvento->setOnChange ( "buscaValorVeiculos('preencheEmpresas')" );

        $this->obCmbTpVeiculoPublicitario = new Select;
        $this->obCmbTpVeiculoPublicitario->setName               ( "inCodTp"               );
        $this->obCmbTpVeiculoPublicitario->addOption             ( "", "Selecione"         );
        $this->obCmbTpVeiculoPublicitario->setCampoId            ( "cod_tipo_veiculos_publicidade"                );
        $this->obCmbTpVeiculoPublicitario->setCampoDesc          ( "descricao"                );
        $this->obCmbTpVeiculoPublicitario->preencheCombo         ( $rsTipoVeiculoPublicitario                   );
        $this->obCmbTpVeiculoPublicitario->setValue              ( $inCodigoTpVeiculoPublicitario             );
        $this->obCmbTpVeiculoPublicitario->setNull               ( false                   );
        $this->obCmbTpVeiculoPublicitario->setStyle              ( "width: 220px"          );
        $this->obCmbTpVeiculoPublicitario->obEvento->setOnChange ( "buscaValorVeiculos('preencheEmpresas')" );

        $this->obTxtCodEmpresa = new TextBox;
        $this->obTxtCodEmpresa->setRotulo    ( "Empresa"  );
        $this->obTxtCodEmpresa->setName      ( "inCodigoEmpresa" );
        $this->obTxtCodEmpresa->setValue     ( $inCodigoEmpresa  );
        $this->obTxtCodEmpresa->setSize      ( 8                   );
        $this->obTxtCodEmpresa->setMaxLength ( 8                   );
        $this->obTxtCodEmpresa->setNull      ( false               );

        $this->obCmbEmpresa = new Select;
        $this->obCmbEmpresa->setName       ( "inCodEmpresa"   );
        $this->obCmbEmpresa->addOption     ( "", "Selecione"    );
        $this->obCmbEmpresa->setCampoId    ( "numcgm"    );
        $this->obCmbEmpresa->setCampoDesc  ( "nom_cgm"    );
        $this->obCmbEmpresa->setValue      ( $inCodigoEmpresa );
        $this->obCmbEmpresa->preencheCombo ( $rsEmpresas      );
        $this->obCmbEmpresa->setNull       ( false              );
        $this->obCmbEmpresa->setStyle      ( "width: 220px"     );
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponenteComposto ( $this->obTxtCodTpVeiculoPublicitario, $this->obCmbTpVeiculoPublicitario );
        $obFormulario->addComponenteComposto ( $this->obTxtCodEmpresa, $this->obCmbEmpresa );
        $obFormulario->addSpan ( $this->obSpnVeiculoPublicitario );
    }
}
?>
