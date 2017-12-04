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
    * Data de Criação: 24/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25841 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 10:02:21 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/10/05 13:00:07  hboaventura
inclusão dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_COMPONENTE );
include_once ( CAM_GP_PAT_COMPONENTES."IPopUpBem.class.php" );

class  IIntervaloPopUpBem extends Componente
{
    /**
        * @access Private
        * @var Object
    */
    public $obIPopUpBemInicial;
    /**
        * @access Private
        * @var Object
    */
    public $obLblBem;
    /**
        * @access Private
        * @var Object
    */
    public $obIPopUpBemFinal;

    public function IIntervaloPopUpBem($obForm)
    {
        parent::Componente();

        $this->setRotulo("Bem");
        $this->setTitle ("Informe o intervalo código do bem.");

        $this->obIPopUpBemInicio = new IPopUpBem( $obForm );
        $this->obIPopUpBemInicio->setId( 'stNomBemInicio' );
        $this->obIPopUpBemInicio->setRotulo( 'Bem Inicial' );
        $this->obIPopUpBemInicio->setTitle( 'Informe o código inicial do bem.' );
        $this->obIPopUpBemInicio->setNull( true );
        $this->obIPopUpBemInicio->setObrigatorioBarra( true );
        $this->obIPopUpBemInicio->obCampoCod->setName( 'inCodBemInicio' );
        $this->obIPopUpBemInicio->obCampoCod->setId( 'inCodBemInicio' );
        $this->obIPopUpBemInicio->setTipoBusca( 'bemBaixado' );
        $this->obIPopUpBemInicio->setMostrarDescricao( false );

        $this->obLblBem = new Label();
        $this->obLblBem->setValue( ' até ' );

        $this->obIPopUpBemFinal = new IPopUpBem( $obForm );
        $this->obIPopUpBemFinal->setId( 'stNomBemFinal' );
        $this->obIPopUpBemFinal->setRotulo( 'Bem Final' );
        $this->obIPopUpBemFinal->setTitle( 'Informe o código final do bem.' );
        $this->obIPopUpBemFinal->setNull( true );
        $this->obIPopUpBemFinal->setObrigatorioBarra( true );
        $this->obIPopUpBemFinal->obCampoCod->setName( 'inCodBemFinal' );
        $this->obIPopUpBemFinal->obCampoCod->setId( 'inCodBemFinal' );
        $this->obIPopUpBemFinal->setTipoBusca( 'bemBaixado' );
        $this->obIPopUpBemFinal->setMostrarDescricao( false );

    }

    public function montaHTML()
    {

        $this->obIPopUpBemInicio->montaHTML();
        $stIPopUpBemInicioHTML = $this->obIPopUpBemInicio->getHTML();

        $this->obLblBem->montaHTML();
        $stObLblBemHTML = $this->obLblBem->getHTML();

        $this->obIPopUpBemFinal->montaHTML();
        $stIPopUpBemFinalHTML = $this->obIPopUpBemFinal->getHTML();

        $obTabela = new Tabela;
        $obTabela->setCellPadding( 0 );
        $obTabela->setCellSpacing( 0 );
        $obTabela->setWidth( 45 );
        $obTabela->setBorder( 0 );
        $obTabela->addLinha();

        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "15" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stIPopUpBemInicioHTML );
        $obTabela->ultimaLinha->commitCelula();

        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldlabel" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "10" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stObLblBemHTML  );
        $obTabela->ultimaLinha->commitCelula();

        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "15" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stIPopUpBemFinalHTML );
        $obTabela->ultimaLinha->commitCelula();

        $obTabela->commitLinha();
        $obTabela->montaHTML();
        $this->setHTML( $obTabela->getHTML() );

    }

}
