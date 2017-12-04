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
    * Arquivo que monta inner do credito
    * Data de Criação: 12/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpCredito.class.php 59914 2014-09-19 20:22:40Z arthur $

    * Casos de uso: uc-05.05.10
*/

/*
Revision 1.4  2007/04/25 20:31:44  cercato
alterando componente para permitir criar mais de um objeto.

Revision 1.3  2006/09/26 14:54:15  dibueno
Atualização do componente BuscaInner

Revision 1.2  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");
include_once ( CLA_BUSCAINNER );

class IPopUpCredito extends BuscaInner
{
    public $obInnerCredito;
    public $obTMONCredito;
    public $stMascaraCredito;
    public $inCodCredito;

    public function setNull($valor) { $this->boNull = $valor; }
    public function getNull() { return $this->boNull; }

    public function IPopUpCredito()
    {
        parent::BuscaInner();

        $this->obTMONCredito = new TMONCredito;
        $this->obTMONCredito->recuperaMascaraCredito( $rsMascara );
        if ( !$rsMascara->eof() ) {
            $this->stMascaraCredito = $rsMascara->getCampo("mascara_credito");
        }
        //$obInnerCredito = new BuscaInner;
        $this->setRotulo    ( "Crédito"         );
        $this->setTitle     ( "Busca crédito."  );
        $this->setId        ( "stCredito"       );
        $this->setNull      ( false             );
        $this->obCampoCod->setName      ("inCodCredito"             );
        $this->obCampoCod->setId        ("inCodCredito"             );
        $this->obCampoCod->setMaxLength ( strlen($this->stMascaraCredito) );
        $this->obCampoCod->setMinLength ( strlen($this->stMascaraCredito) );
        $this->obCampoCod->setMascara   ( $this->stMascaraCredito   );
   }

   public function setCodCredito($inValor)
   {
        $this->inCodCredito = $inValor;
   }

    public function geraFormulario(&$obFormulario)
    {
        ;

        if ($_REQUEST['stCodEntidade'] == 'cod_entidade') {
        $this->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','todos','".Sessao::getId()."&stCodEntidade='+frm.inCodEntidade.value+'&stTipoReceita=".$_REQUEST['stTipoReceita']."','800','550');" );
        } else {
        $this->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','todos','".Sessao::getId()."&stCodEntidade=".$_REQUEST['stCodEntidade']."&stTipoReceita=".$_REQUEST['stTipoReceita']."','800','550');" );
        }

           if ($this->inCodCredito) {
           if (strlen($this->inCodCredito) >= strlen($this->stMascaraCredito) ) {
               $inCodCreditoComposto = explode('.', $this->inCodCredito );
               $stFiltro = "WHERE ";
               $stFiltro .= " \n exercicio      = ".Sessao::getExercicio()." AND ";
               if ($_REQUEST['stCodEntidade'])
               $stFiltro .= " \n cod_entidade   = ".$_REQUEST['stCodEntidade']." AND ";
               $stFiltro .= " \n mc.cod_credito = ".$inCodCreditoComposto[0]." AND ";
               $stFiltro .= " \n me.cod_especie = ".$inCodCreditoComposto[1]." AND ";
               $stFiltro .= " \n mg.cod_genero = ".$inCodCreditoComposto[2]." AND ";
               $stFiltro .= " \n mn.cod_natureza = ".$inCodCreditoComposto[3];
               $this->obTMONCredito->recuperaRelacionamento( $rsGrupos, $stFiltro );
               if ( !$rsGrupos->eof() ) {
                  $this->setValue( $rsGrupos->getCampo("descricao_credito") );
                  $this->obCampoCod->setValue( $this->inCodCredito );
               }
            }
           } else {
           $this->setValue( "&nbsp;" );
           $this->obCampoCod->setValue( null );
        }

        $pgOcul = CAM_GT_MON_INSTANCIAS."credito/OCManterCredito.php?".Sessao::getId();
        if ($_REQUEST['stCodEntidade'] == 'cod_entidade') {

        $stOnChange = "ajaxJavaScript('".$pgOcul."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stCodEntidade='+frm.inCodEntidade.value+'&stTipoReceita=".$_REQUEST['stTipoReceita']."','PreencheCredito');";
        } else {
        $stOnChange = "ajaxJavaScript('".$pgOcul."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stCodEntidade=".$_REQUEST['stCodEntidade']."&stTipoReceita=".$_REQUEST['stTipoReceita']."','PreencheCredito');";
        }
        $this->obCampoCod->obEvento->setOnChange( $stOnChange );

        $obFormulario->addComponente( $this );
   }

}
?>
