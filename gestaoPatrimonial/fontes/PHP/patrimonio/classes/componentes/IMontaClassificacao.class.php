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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: IMontaClassificacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once (CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );

class  IMontaClassificacao extends Objeto
{
    public $obTxtCodClassificacao;
    public $obTPatrimonioNatureza;
    public $obTPatrimonioGrupo;
    public $obTPatrimonioEspecie;
    public $obISelectEspecie;
    public $stMascara;
    public $boNull;

    public function setNull($var)
    {
        $this->boNull = $var;
    }
    public function getNull()
    {
        return $this->boNull;
    }

    public function IMontaClassificacao(&$obForm)
    {
        $this->setNull( false );

        //instancia o componente ISelectEspecie
        $this->obISelectEspecie = new ISelectEspecie( $obForm );

        //executa métodos para pegar o max de cada tabela para compor a máscara da classificacao
        $this->obTPatrimonioNatureza = new TPatrimonioNatureza();
        $this->obTPatrimonioNatureza->proximoCod( $inMaxCodNatureza );

        $this->obTPatrimonioGrupo = new TPatrimonioGrupo();
        $this->obTPatrimonioGrupo->proximoCod( $inMaxCodGrupo );

        $this->obTPatrimonioEspecie = new TPatrimonioEspecie();
        $this->obTPatrimonioEspecie->proximoCod( $inMaxCodEspecie );

        $this->stMascara = str_pad('9',strlen($inMaxCodNatureza-1),'9').'.'.str_pad('9',strlen($inMaxCodGrupo-1),'9').'.'.str_pad('9',strlen($inMaxCodEspecie-1),'9');

        //instancia o componente TextBox para o código da classificacao
        $this->obTxtCodClassificacao = new TextBox();
        $this->obTxtCodClassificacao->setName( 'stCodClassificacao' );
        $this->obTxtCodClassificacao->setId( 'stCodClassificacao' );
        $this->obTxtCodClassificacao->setRotulo( 'Classificação' );
        $this->obTxtCodClassificacao->setTitle( 'Informe a classificação do bem.' );
        $this->obTxtCodClassificacao->setsize( strlen($inMaxCodEspecie-1) + strlen($inMaxCodGrupo-1) + strlen($inMaxCodNatureza-1) );
        $this->obTxtCodClassificacao->setMascara( $this->stMascara );
        $this->obTxtCodClassificacao->setNull( false );
    }

    public function geraFormulario(&$obFormulario)
    {

        $this->obTxtCodClassificacao->setNull( $this->getNull() );
        $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->setNull( $this->getNull() );
        $this->obISelectEspecie->obISelectGrupo->obSelectGrupo->setNull( $this->getNull() );
        $this->obISelectEspecie->obSelectEspecie->setNull( $this->getNull() );

        $pgOcul  = CAM_GP_PAT_PROCESSAMENTO.'OCIMontaClassificacao.php?'.Sessao::getId();

        $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->setOnChange( $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->getOnChange()."ajaxJavaScript( '".$pgOcul."','&inCodNatureza='+this.value+'&stCtrl=preencheClassificacao' );");
        $this->obISelectEspecie->obISelectGrupo->obSelectGrupo->obEvento->setOnChange( $this->obISelectEspecie->obISelectGrupo->obSelectGrupo->obEvento->getOnChange()."ajaxJavaScript( '".$pgOcul."','&inCodNatureza='+$('".$this->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').value+'&inCodGrupo='+this.value+'&stCtrl=preencheClassificacao' );");
        $this->obISelectEspecie->obSelectEspecie->obEvento->setOnChange($this->obISelectEspecie->obSelectEspecie->obEvento->getOnChange()."ajaxJavaScript( '".$pgOcul."','&inCodNatureza='+$('".$this->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').value+'&inCodGrupo='+$('".$this->obISelectEspecie->obISelectGrupo->obSelectGrupo->getId()."').value+'&inCodEspecie='+this.value+'&stCtrl=preencheClassificacao' );");
//        $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->setOnChange( $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->getOnChange().';');
//        $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->setOnChange( $this->obISelectEspecie->obISelectGrupo->obISelectNatureza->obEvento->getOnChange().';');

        $this->obTxtCodClassificacao->obEvento->setOnBlur( "ajaxJavaScript( '".$pgOcul."','&stCodClassificacao='+this.value+'&stCtrl=preencheCombos' );" );

        //coloca na sessao o componente IMontaClassificacao
        Sessao::write('IMontaClassificacao', $this);

        $obFormulario->addComponente( $this->obTxtCodClassificacao );
        $this->obISelectEspecie->geraFormulario( $obFormulario );

    }

}
