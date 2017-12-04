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
    * Arquivo do componente para funcao
    * Data de Criação: 13/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.03.95

*/

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );

class IPopUpFuncao extends Objeto
{
    public $obInnerFuncao;
    public $obTFuncao;
    public $inCodFuncao;
    public $stMascara;
    public $stTipoFuncao;
    public $inCodModulo;
    public $inCodBiblioteca;

    /**
    * setTipoFuncaoBusca
    *
    *@param valor (null(padrao), interna, externa) string
    *
    */
    public function setTipoFuncaoBusca($valor) { $this->stTipoFuncao = $valor;}

    /**
    * getTipoFuncaoBusca
    *
    *@return stTipoFuncao string
    *
    */
    public function getTipoFuncaoBusca() { return $this->stTipoFuncao;}

    public function IPopUpFuncao()
    {
        $this->setTipoFuncaoBusca("externa");

        $this->obTFuncao = new TAdministracaoFuncao;
        //Rotina para pegar a mascara do banco de dados
        $this->stMascara = $this->obTFuncao->recuperaMascaraFuncao();
        $this->obInnerFuncao = new BuscaInner;
        $this->obInnerFuncao->setNull             ( false );
        $this->obInnerFuncao->setTitle            ( "Busca função." );
        $this->obInnerFuncao->setRotulo           ( "Função" );
        $this->obInnerFuncao->setId               ( "stFuncao"  );
        $this->obInnerFuncao->obCampoCod->setName ( "inCodFuncao" );
        $this->obInnerFuncao->obCampoCod->setInteiro ( true );
        $this->inCodModulo = 0;
        $this->inCodBiblioteca = 0;
   }

   public function setCodFuncao($inValor)
   {
       $this->inCodFuncao = $inValor;
   }

   public function setCodModulo($inValor)
   {
       $this->inCodModulo = $inValor;
   }

   public function setCodBiblioteca($inValor)
   {
       $this->inCodBiblioteca = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        if ($this->inCodModulo && $this->inCodBiblioteca) {
            $stTMP = Sessao::getId()."&stCodModulo=".$this->inCodModulo."&stCodBiblioteca=".$this->inCodBiblioteca."&";
        } else {
            $stTMP = Sessao::getId();
        }

        $this->obInnerFuncao->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFuncao','".$this->getTipoFuncaoBusca()."','".$stTMP."','800','550');" );

        if ($this->stMascara) {
            $this->obInnerFuncao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
            $this->obInnerFuncao->obCampoCod->setMinLength ( strlen($this->stMascara) );
            $this->obInnerFuncao->obCampoCod->setSize ( strlen($this->stMascara) );
        }

        if ($this->inCodFuncao) {
            $arCodFuncao = explode('.', $this->inCodFuncao );
            $this->obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $this->obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
            $this->obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
            $this->obTFuncao->recuperaPorChave( $rsFuncao );
            if ( !$rsFuncao->Eof() ) {
                $this->obInnerFuncao->setValue( $rsFuncao->getCampo("nom_funcao") );
                $this->obInnerFuncao->obCampoCod->setValue( $this->inCodFuncao );
            }
        }

        $pgOcul = CAM_GA_ADM_INSTANCIAS."geradorcalculo/OCManterFuncao.php?".Sessao::getId();
        if ($this->inCodModulo && $this->inCodBiblioteca) {
            $stOnChange = "ajaxJavaScript('".$pgOcul."&inCodFuncao='+this.value+'&stCodModulo=".$this->inCodModulo."&stCodBiblioteca=".$this->inCodBiblioteca."','preencheFuncao');";
        } else {
            $stOnChange = "ajaxJavaScript('".$pgOcul."&inCodFuncao='+this.value,'preencheFuncao');";
        }

        $this->obInnerFuncao->obCampoCod->obEvento->setOnChange( $stOnChange );

        $obFormulario->addComponente( $this->obInnerFuncao );
   }

}
?>
