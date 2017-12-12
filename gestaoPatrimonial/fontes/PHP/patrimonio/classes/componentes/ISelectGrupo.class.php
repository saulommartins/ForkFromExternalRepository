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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.5  2007/10/05 13:00:07  hboaventura
inclusão dos arquivos

Revision 1.4  2007/09/18 15:36:20  hboaventura
Adicionando ao repositório

Revision 1.3  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:27  diego

*/

class  ISelectGrupo extends Objeto
{
var $obForm;
//var $obTPatrimonioGrupo;
var $obISelectNatureza;

function ISelectGrupo($obForm)
{
    include_once (CAM_GP_PAT_COMPONENTES."ISelectNatureza.class.php" );
    //include_once (TPAT."TPatrimonioGrupo.class.php" );

    $this->obForm = $obForm;
    //$this->obTPatrimonioGrupo = new TPatrimonioGrupo();
    $this->obISelectNatureza  =  new ISelectNatureza($obForm);
    $this->obSelectGrupo      =  new Select();
    $this->obSelectGrupo->setName   ('inCodGrupo');
    $this->obSelectGrupo->setId     ('inCodGrupo');
    $this->obSelectGrupo->setValue  ($inCodGrupo);
    $this->obSelectGrupo->setStyle  ( "width: 270px; ");
    $this->obSelectGrupo->setRotulo ( "Grupo" );
    $this->obSelectGrupo->setTitle  ( "Informe o grupo." );
    $this->obSelectGrupo->setNull   ( false );
    $this->obSelectGrupo->addOption ("", "Selecione");
}

function geraFormulario(&$obFormulario)
{
    $pgOcul  = CAM_GP_PAT_PROCESSAMENTO.'OCISelectGrupo.php?'.Sessao::getId();
    $pgOcul .= '&stNatureza='.$this->obISelectNatureza->getId();
    $pgOcul .= '&stGrupo='.$this->obSelectGrupo->getId();
    $this->obISelectNatureza->obEvento->setOnChange ("ajaxJavaScript('".$pgOcul."&inCodNatureza='+this.value,'montaGrupo');".$this->obISelectNatureza->obEvento->getOnChange());
    $obFormulario->addComponente($this->obISelectNatureza);
    $obFormulario->addComponente($this->obSelectGrupo);
}

}
?>
