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
* Arquivo de popup de busca Número Edital
* Data de Criação: 25/10/2006

* @author Analista: Gelson
* @author Desenvolvedor: Tonismar Régis Bernardo

$Revision: 24859 $
$Name$
$Author: bruce $
$Date: 2007-08-16 12:38:30 -0300 (Qui, 16 Ago 2007) $

* Casos de uso: uc-03.05.16
*/

/*

$Log$
Revision 1.4  2007/08/16 15:38:00  bruce
Bug#9824#

Revision 1.3  2007/03/07 19:57:16  hboaventura
Correção no componente para evitar a digitação de aspas e também setar o tamanho da máscara de acordo com o número de registros do sistema

Revision 1.2  2006/11/16 16:11:11  hboaventura
Comentado a função setValoresBusca, pois estava gerando conteúdo no frame Oculto

Revision 1.1  2006/10/27 10:19:39  tonismar
Componente IPopUpNumeroEdital

*/

include_once( CLA_BUSCAINNER );

class  IPopUpNumeroEdital extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;

    /**
        * @access Private
        * @var string
    */
    public $stFiltroEditais;

    /**
        * @access Private
        * @var string
    */
    public $vaidarEdital;

    public function setFiltroEditaisAdicional($valor) { $this->stFiltroEditais = $valor;}
    public function getFiltroEditaisAdicional() { return $this->stFiltroEditais;}

    public function setValidacaoPadraoEdital($validar) { $this->validarEdital = $validar;}
    public function getValidacaoPadraoEdital() { return $this->validarEdital;}

    /**
        * Metodo Construtor
        * @access Public

    */
    public function IPopUpNumeroEdital($obForm)
    {
        parent::BuscaInner();

        $ultCodEdital = SistemaLegado::pegaDado("num_edital","licitacao.edital", " where exercicio = '".Sessao::getExercicio()."' order by exercicio desc, num_edital desc limit 1");

        if ( $ultCodEdital )
            $tam = strlen($ultCodEdital);
        else
            $tam = 1;

        $stMascara = "";
        $stMascara = str_pad( $stMascara , $tam , '9' ) . "/9999";

        $this->obForm = $obForm;
        $this->setRotulo                ( 'Número do Edital' );
        $this->setTitle                 ( 'Selecione o número do edital.' );
        $this->setId                    ( 'NumeroEdital'     );
        $this->setMostrarDescricao		( false              );

        $this->setCampoCod          	( ""                 );
        $this->setCampoCod				( new TextBox()      );
        $this->obCampoCod->setMinLength ( 1                  );
        $this->obCampoCod->setRotulo	( $this->getRotulo() );
        $this->obCampoCod->setName      ( "stNumeroEdital"   );
        $this->obCampoCod->setMascara   ( $stMascara         );

        $this->setValidacaoPadraoEdital(true);

        $this->obCampoCod->obEvento->setOnChange('if (this.value != \'\') {this.value = preencheProcessoComZeros( this.value,\''.$stMascara.'\', \''.Sessao::getExercicio().'\');}');
    }

    public function montaHTML()
    {
        global $request;
        $stAcao = $request->get('stAcao');

        Sessao::write('filtroAdicionalSqlEditais',$this->getFiltroEditaisAdicional());

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_LIC_POPUPS . "processoLicitatorio/FLBuscaNumeroEdital.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() ." &stAcao=".$stAcao."','800','550');");

        if ($this->getValidacaoPadraoEdital()) {
            $url = CAM_GP_LIC_POPUPS."processoLicitatorio/OCBuscaNumeroEdital.php?".Sessao::getId();
            $this->obCampoCod->obEvento->setOnBlur($this->obCampoCod->obEvento->getOnBlur()." if (this.value != '') { ajaxJavaScript('".$url."&".$this->obCampoCod->getName()."=' + this.value + '&stField=' + this.name + '&stFieldId=' +this.id, 'validaEdital'); } ");
        }

        parent::montaHTML();
    }
}
?>
