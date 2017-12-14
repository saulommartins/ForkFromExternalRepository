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
* Arquivo de popup de busca de Autoridade
* Data de Criação: 11/09/2006

* @author Analista: Fabio Bertoldi
* @author Desenvolvedor: Diego Bueno Coelho

* @package URBEM
* @subpackage

    * $Id: IPopUpAutoridade.class.php 61270 2014-12-26 17:32:48Z evandro $

* Casos de uso: uc-05.04.08
*/

/*
$Log$
Revision 1.2  2006/09/28 08:49:48  dibueno
Alteração no nome do campo texto do componente

Revision 1.1  2006/09/26 11:14:48  dibueno
*** empty log message ***

Revision 1.4  2006/09/19 15:51:32  domluc
Correção para o Bug #7009

Revision 1.3  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS
*/

include_once ( CLA_BUSCAINNER );

class  IPopUpAutoridade extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;
    public $inCodAutoridade;
    public $stAutoridade;
    public $boNull = false;

    public function setNull($valor) { $this->boNull = $valor; }
    public function getNull() { return $this->boNull; }
    public function setCodAutoridade($inValor) { $this->inCodAutoridade = $inValor; }
    public function getCodAutoridade() { return $this->inCodAutoridade; }

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpAutoridade()
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Autoridade'       );
        $this->setTitle                  ( ''                 );
        $this->setId                     ( 'stNomAutoridade'  );
        $this->setNull                   ( $this->getNull()   );

        $this->obCampoCod->setName       ( "inCodAutoridade"  );
        $this->obCampoCod->setSize       ( 6                  );
        $this->obCampoCod->setMaxLength  ( 10                 );
        $this->obCampoCod->setAlign      ( "left"             );

        $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {

        if ( $this->getCodAutoridade() ) {

            include_once(CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php");
            $obTDATAutoridade = new TDATAutoridade();
            $stFiltro = "\n da.cod_autoridade = ". $this->inCodAutoridade ." AND ";
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

            $obTDATAutoridade->recuperaListaAutoridade( $rsRecordSet, $stFiltro );

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('cod_autoridade') );
            $this->setValue( $rsRecordSet->getCampo('autoridade') );
        }

        $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."autoridade/OCManterAutoridade.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaAutoridade' );" );

        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_DAT_POPUPS . "autoridade/FLProcurarAutoridade.php','frm', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        //parent::montaHTML();
        $obFormulario->addComponente ( $this );

    }
}
?>
