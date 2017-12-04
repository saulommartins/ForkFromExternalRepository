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
    * Classe de regra de interface para localização
    * Data de Criação: 11/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package administracao
    * @subpackage componentes

    $Id: IMontaLocalizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-01.01.00
*/

include_once( CAM_GA_ADM_MAPEAMENTO."TOrgao.class.php" );

class IMontaLocalizacao extends Objeto
{

    public $obTxtLocalizacao;
    public $obSelectOrgao;
    public $obSelectUnidade;
    public $obSelectDepartamento;
    public $obSelectSetor;
    public $obSelectLocal;
    public $boNull;

    public function setNull($var)
    {
        $this->boNull = $var;
    }
    public function getNull()
    {
        return $this->boNull;
    }

    public function IMontaLocalizacao()
    {

        //cria o componente TextBox para o código de localização
        $this->setNull( false );

        $this->obTxtLocalizacao = new TextBox();
        $this->obTxtLocalizacao->setRotulo   ( 'Localização' );
        $this->obTxtLocalizacao->setTitle    ( 'Informe a localização do bem.' );
        $this->obTxtLocalizacao->setName     ( 'stCodLocalizacao' );
        $this->obTxtLocalizacao->setId       ( 'stCodLocalizacao' );

        $this->obTxtLocalizacao->setMascara  ( sistemaLegado::pegaConfiguracao( 'mascara_local',2 ) );
        $this->obTxtLocalizacao->setMaxLength( 23 );
        $this->obTxtLocalizacao->setSize     ( 23 );

        //pega a mascara da localizacao
        $arMascara = explode( '.',sistemaLegado::pegaConfiguracao( 'mascara_local',2 ) );

        //retorna todos os orgaos da base
        $obTOrgao = new TOrgao();
        $obTOrgao->recuperaTodos( $rsOrgao );
        $inCount = 0;
        foreach ($rsOrgao->arElementos as $arOrgao) {
            $rsOrgao->arElementos[$inCount]['cod_orgao'] = str_pad($arOrgao['cod_orgao'],strlen($arMascara[0]),0,STR_PAD_LEFT);
            $inCount++;
        }

        //cria o componente Select para o Orgao
        $this->obSelectOrgao = new Select  ();
        $this->obSelectOrgao->setRotulo    ( 'Órgão' );
        $this->obSelectOrgao->setTitle     ( 'Informe o órgão do bem.' );
        $this->obSelectOrgao->setName      ( 'inCodOrgao' );
        $this->obSelectOrgao->setId        ( 'inCodOrgao' );
        $this->obSelectOrgao->setCampoId   ( '[cod_orgao]/[ano_exercicio]' );
        $this->obSelectOrgao->setCampoDesc ( '[nom_orgao] - [ano_exercicio]' );
        $this->obSelectOrgao->addOption    ( '','Selecione' );
        $this->obSelectOrgao->preencheCombo( $rsOrgao );
        $this->obSelectOrgao->setStyle	   ( 'width:300px;' );

        //cria o componente Select para o Unidade
        $this->obSelectUnidade = new Select();
        $this->obSelectUnidade->setRotulo  ( 'Unidade' );
        $this->obSelectUnidade->setTitle   ( 'Informe a unidade do bem.' );
        $this->obSelectUnidade->setName    ( 'inCodUnidade' );
        $this->obSelectUnidade->setId      ( 'inCodUnidade' );
        $this->obSelectUnidade->addOption  ( '','Selecione' );
        $this->obSelectUnidade->setStyle   ( 'width:300px;' );

        //cria o componente Select para o Departamento
        $this->obSelectDepartamento = new Select();
        $this->obSelectDepartamento->setRotulo  ( 'Departamento' );
        $this->obSelectDepartamento->setTitle   ( 'Informe o departamento do bem.' );
        $this->obSelectDepartamento->setName    ( 'inCodDepartamento' );
        $this->obSelectDepartamento->setId      ( 'inCodDepartamento' );
        $this->obSelectDepartamento->addOption  ( '','Selecione' );
        $this->obSelectDepartamento->setStyle   ( 'width:300px;' );

        //cria o componente Select para o setor
        $this->obSelectSetor = new Select();
        $this->obSelectSetor->setRotulo  ( 'Setor' );
        $this->obSelectSetor->setTitle   ( 'Informe o setor do bem.' );
        $this->obSelectSetor->setName    ( 'inCodSetor' );
        $this->obSelectSetor->setId      ( 'inCodSetor' );
        $this->obSelectSetor->addOption  ( '','Selecione' );
        $this->obSelectSetor->setStyle   ( 'width:300px;' );

        //cria o componente Select para o local
        $this->obSelectLocal = new Select();
        $this->obSelectLocal->setRotulo  ( 'Local' );
        $this->obSelectLocal->setTitle   ( 'Informe o local do bem.' );
        $this->obSelectLocal->setName    ( 'inCodLocal' );
        $this->obSelectLocal->setId      ( 'inCodLocal' );
        $this->obSelectLocal->addOption  ( '','Selecione' );
        $this->obSelectLocal->setStyle   ( 'width:300px;' );

    }

    public function geraFormulario(&$obFormulario)
    {

        $pgOcul = CAM_GA_ADM_PROCESSAMENTO."OCIMontaLocalizacao.php?".Sessao::getId();

        //concatena os campos necessarios para cada combo
        $stCampos = "'".$pgOcul."&".$this->obTxtLocalizacao->getName()."='+$('".$this->obTxtLocalizacao->getId()."').value+'&".$this->obSelectOrgao->getName()."='+$('".$this->obSelectOrgao->getId()."').value";
        $this->obTxtLocalizacao->obEvento->setOnBlur( "ajaxJavaScript(".$stCampos.",'preencheCombos');" );
        $this->obSelectOrgao->obEvento->setOnChange( "ajaxJavaScript(".$stCampos.",'preencheComboUnidade');" );

        $stCampos.= "+'&".$this->obSelectUnidade->getName()."='+$('".$this->obSelectUnidade->getId()."').value";
        $this->obSelectUnidade->obEvento->setOnChange( "ajaxJavaScript(".$stCampos.",'preencheComboDepartamento');" );

        $stCampos.= "+'&".$this->obSelectDepartamento->getName()."='+$('".$this->obSelectDepartamento->getId()."').value";
        $this->obSelectDepartamento->obEvento->setOnChange( "ajaxJavaScript(".$stCampos.",'preencheComboSetor');" );

           $stCampos.= "+'&".$this->obSelectSetor->getName()."='+$('".$this->obSelectSetor->getId()."').value";
        $this->obSelectSetor->obEvento->setOnChange( "ajaxJavaScript(".$stCampos.",'preencheComboLocal');" );

        $stCampos.= "+'&".$this->obSelectLocal->getName()."='+$('".$this->obSelectLocal->getId()."').value";
        $this->obSelectLocal->obEvento->setOnChange( "ajaxJavaScript(".$stCampos.",'preencheComboCodLocalizacao');" );

        $this->obTxtLocalizacao->setNull     ( $this->getNull() );
        $this->obSelectOrgao->setNull      ( $this->getNull() );
        $this->obSelectUnidade->setNull    ( $this->getNull() );
        $this->obSelectDepartamento->setNull    ( $this->getNull() );
        $this->obSelectLocal->setNull    ( $this->getNull() );
        $this->obSelectSetor->setNull    ( $this->getNull() );

         #sessao->componentes['obIMontaLocalizacao'] = serialize($this);
         Sessao::write('obIMontaLocalizacao',$this);
        $obFormulario->addComponente( $this->obTxtLocalizacao );
        $obFormulario->addComponente( $this->obSelectOrgao );
        $obFormulario->addComponente( $this->obSelectUnidade );
        $obFormulario->addComponente( $this->obSelectDepartamento );
        $obFormulario->addComponente( $this->obSelectSetor );
        $obFormulario->addComponente( $this->obSelectLocal );
    }

}
