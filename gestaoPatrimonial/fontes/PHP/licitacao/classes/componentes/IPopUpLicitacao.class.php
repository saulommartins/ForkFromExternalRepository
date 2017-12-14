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
    * Monta estrutura de componentes para selecionar uma licitação
    * Data de Criação: 11/10/2006

    * @author Analista: Lucas Teixeia Stephanou
    * @author Desenvolvedor: Lucas Teixeia Stephanou

    * @package URBEM
    * @subpackage

    $Revision: 23608 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-06-28 15:48:57 -0300 (Qui, 28 Jun 2007) $

    * Casos de uso: uc-03.05.23, uc-03.05.22
*/

/*
$Log$
Revision 1.4  2007/06/28 18:48:57  hboaventura
Bug#9493#

Revision 1.3  2006/11/27 11:59:32  leandro.zis
atualizado

Revision 1.2  2006/11/22 21:30:47  leandro.zis
atualizado

Revision 1.1  2006/11/01 19:56:57  leandro.zis
atualizado

*/

class IPopUpLicitacao extends Objeto
{
    public $obBuscaInner;
    public $obSpanInfoAdicional;

    public function IPopUpLicitacao(&$obForm)
    {
        parent::Objeto();
        $this->obBuscaInner = new BuscaInner;
        $this->obBuscaInner->obForm = &$obForm;

        $this->obBuscaInner->setRotulo                ( 'Número da Licitação' );
        $this->obBuscaInner->setTitle                 ( 'Selecione a licitação na PopUp de busca.' );
        $this->obBuscaInner->obCampoCod->setName      ( 'inCodLicitacao'  );
        $this->obBuscaInner->obCampoCod->setId        ( 'inCodLicitacao'  );
        $this->obBuscaInner->obCampoCod->setAlign     ( "left" );
        $this->obBuscaInner->obCampoCod->setReadOnly  ( TRUE );
        $this->obBuscaInner->setId                    ( 'txtLicitacao' );
        $this->obBuscaInner->setNull                  ( true );
        $this->obBuscaInner->setMostrarDescricao		( false		       );
        $this->obBuscaInner->stTipoBusca = 'popup';
        $this->obBuscaInner->setFuncaoBusca("abrePopUp('".CAM_GP_LIC_POPUPS."licitacao/FLProcurarLicitacao.php','".$this->obBuscaInner->obForm->getName()."','".$this->obBuscaInner->obCampoCod->getName()."','".$this->obBuscaInner->getId()."','".$this->obBuscaInner->stTipoBusca."','".Sessao::getId()."','800','550');");

        $this->obBuscaInner->setValoresBusca( CAM_GP_COM_POPUPS."licitacao/OCProcuraLicitacao.php?".Sessao::getId(), $this->obBuscaInner->obForm->getName() );

        $this->obSpanInfoAdicional = new Span;
        $this->obSpanInfoAdicional->setId('spnInfoAdicional');
        $this->obHdnCodEntidade = new Hidden;
        $this->obHdnCodEntidade->setId('inCodEntidade');
        $this->obHdnCodEntidade->setName('inCodEntidade');
        $this->obHdnCodModalidade = new Hidden;
        $this->obHdnCodModalidade->setId('inCodModalidade');
        $this->obHdnCodModalidade->setName('inCodModalidade');
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente    ( $this->obBuscaInner );
        //$obFormulario->addSpan          ( $this->obSpanInfoAdicional );
        $obFormulario->addHidden        ( $this->obHdnCodEntidade );
        $obFormulario->addHidden        ( $this->obHdnCodModalidade );
    }

}
?>
