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
    * Arquivo que monta inner de busca empresas
    * Data de Criação: 13/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpEmpresa.class.php 59633 2014-09-03 12:38:28Z carolina $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.6  2007/03/07 12:38:13  rodrigo
Bug #5874#

Revision 1.5  2007/03/01 20:02:34  rodrigo
Bug #5874#

Revision 1.4  2006/10/17 11:12:39  dibueno
Adição de opções para poder escolher metodo de busca, dentre outras.

Revision 1.3  2006/09/15 11:57:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IPopUpEmpresa extends Objeto
{
    public $obInnerEmpresa;
    public $obTCEMCadastroEconomico;
    public $obTAdministracaoConfiguracao;
    public $inInscricaoEconomica;
    public $stMascaraInscricao;
    public $boNull;
    public $stFuncaoBusca;
    public $stFuncaoChange;
    public $stPgOculta;
    public $boVerificaInscricao;

    public function setNull($valor) { $this->boNull = $valor;   }
    public function getNull() { return $this->boNull;     }
    public function setFuncaoBuscaComponente($valor) { $this->stFuncaoBusca = $valor;   }
    public function getFuncaoBuscaComponente() { return $this->stFuncaoBusca;     }
    public function setPgOculta($valor) { $this->stPgOculta = $valor; }
    public function getPgOculta() { return $this->stPgOculta; }
    public function setFuncaoChange($valor) { $this->stFuncaoChange = $valor;   }
    public function getFuncaoChange() { return $this->stFuncaoChange;     }

    public function IPopUpEmpresa()
    {
        ;

        $this->obTCEMCadastroEconomico = new TCEMCadastroEconomico;

        $this->obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", 14 );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica");
        $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );
        if ( !$rsConfiguracao->Eof() ) {
            $this->stMascaraInscricao = $rsConfiguracao->getCampo( "valor" ) ;
        }

        $this->obInnerEmpresa = new BuscaInner;
        $this->obInnerEmpresa->setRotulo               ( "Inscrição Econômica" );
        $this->obInnerEmpresa->setTitle                ( "Pessoa Física ou Jurídica cadastrada como Inscrição Econômica.");
        $this->obInnerEmpresa->setId                   ( "stInscricaoEconomica"             );
        $this->obInnerEmpresa->obCampoCod->setNull     ( $this->getNull ()                  );
        $this->obInnerEmpresa->obCampoCod->setName     ( "inInscricaoEconomica"             );
        $this->obInnerEmpresa->obCampoCod->setSize     ( strlen( $this->stMascaraInscricao ));
        $this->obInnerEmpresa->obCampoCod->setMaxLength( strlen( $this->stMascaraInscricao ));
        $this->obInnerEmpresa->obCampoCod->setMascara  ( $this->stMascaraInscricao   );
        $this->obInnerEmpresa->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );
    }

    public function setVerificaInscricao($valor)
    {
        $this->boVerificaInscricao = $valor;
    }

    public function setInscricaoEconomica($inValor)
    {
        $this->inInscricaoEconomica = $inValor;
    }

    public function geraFormulario(&$obFormulario)
    {
        $boVerificaInscricao = isset($boVerificaInscricao) ? $boVerificaInscricao : "" ;

        if ($this->inInscricaoEconomica) {
                $stFiltro = " AND CE.inscricao_economica = ".$this->inInscricaoEconomica;
                $this->obTCEMCadastroEconomico->recuperaListaConsulta( $rsEmpresas, $stFiltro );
                if ( !$rsEmpresas->eof() ) {
                    $this->obInnerEmpresa->setValue( $rsEmpresas->getCampo("nom_cgm") );
                    $this->obInnerEmpresa->obCampoCod->setValue( $this->inInscricaoEconomica );
                }
        }
        if ( $this->getFuncaoBuscaComponente () ) {
            $this->obInnerEmpresa->setFuncaoBusca          ( $this->getFuncaoBuscaComponente () );
            echo ' teste <br> '.$this->getFuncaoBuscaComponente ();
        }

        $pgOcul = CAM_GT_CEM_INSTANCIAS."inscreconomica/OCManterInscricao.php?".Sessao::getId();
        if ( $this->getPgOculta () ) {
            $pgOcul = $this->getPgOculta().Sessao::getId();
        }

        $stFuncaoChange = 'PreencheEmpresa';
        if ( $this->getFuncaoChange () ) {
            $stFuncaoChange = $this->getFuncaoChange ();
        }


         $stOnChange = "ajaxJavaScriptSincrono('".$pgOcul."&inInscricaoEconomica='+this.value,'".$stFuncaoChange."');";
         $this->obInnerEmpresa->obCampoCod->obEvento->setOnChange( $stOnChange );
   

        $obFormulario->addComponente( $this->obInnerEmpresa );
    }

}
?>
