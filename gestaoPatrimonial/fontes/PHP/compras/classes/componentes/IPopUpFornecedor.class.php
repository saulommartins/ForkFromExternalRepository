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
* Arquivo de popup de busca de Fornecedor
* Data de Criação: 12/09/2003

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

* @package URBEM
* @subpackage

$Revision: 19121 $
$Name$
$Author: bruce $
$Date: 2007-01-05 09:21:55 -0200 (Sex, 05 Jan 2007) $

 Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.4  2007/01/05 11:19:38  bruce
Bug #7898#
Bug #7806#

Revision 1.3  2006/10/02 17:59:11  tonismar
incluído id na imagem

Revision 1.2  2006/09/14 09:11:31  cleisson
Criação do componente fornecedor

Revision 1.1  2006/09/14 09:05:05  cleisson
Criação do componente fornecedor

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpFornecedor extends BuscaInner
{

    public $stTipoConsulta;   //// se estiver vazia a consulta pegar qualquer tipo de fornecedor
                          //// certificado: pega apenas os que tiverem registro em licitacao.participante_certificacao

    public function setTipoConsulta($valor) { $this->stTipoConsulta = $valor; }
    public function getTipoConsulta() { return $this->stTipoConsulta;            }
    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpFornecedor($obForm)
    {
        parent::BuscaInner();
        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Fornecedor'           );
        $this->setTitle                  ( 'Informe o fornecedor.');
        $this->setId                     ( 'stNomCGM'             );
        $this->setNull                   ( true                   );
        $this->obCampoCod->setName       ( "inCodFornecedor"      );
        $this->obCampoCod->setAlign      ( "left"                 );
        $this->obImagem->setId           ( "imgFornecedor"        );

    }

    public function montaHTML()
    {

        $pgOcul = "'".CAM_GP_COM_POPUPS."fornecedor/OCProcurarFornecedor.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stTipoConsulta=".$this->getTipoConsulta()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');" );

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_COM_POPUPS . "fornecedor/FLProcurarFornecedor.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() ."&stTipoConsulta=".$this->getTipoConsulta().
"','800','550');");

        //$this->setValoresBusca( CAM_GP_COM_POPUPS.'fornecedor/OCProcurarFornecedor.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );

       parent::montaHTML();
    }
}
?>
