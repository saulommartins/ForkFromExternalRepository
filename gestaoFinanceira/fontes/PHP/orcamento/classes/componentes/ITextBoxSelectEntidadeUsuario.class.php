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
    * Arquivo de textbox e select entidade geral
    * Data de Criação: 22/06/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage

    $Revision: 30824 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-03-15 16:27:34 -0300 (Qui, 15 Mar 2007) $

    * Casos de uso: uc-02.01.02
                    uc-03.05.15
*/

/*
$Log$
Revision 1.11  2007/03/15 19:27:34  hboaventura
Bug #8203#

Revision 1.10  2007/01/10 15:33:28  bruce
Bug #8012#

Revision 1.9  2007/01/04 15:50:55  bruce
fiz não selecionar automaticamente a entidade quando só tem uma.

Revision 1.8  2007/01/03 11:09:03  cako
Bug #7792#

Revision 1.7  2006/11/13 20:15:03  rodrigo
Bug #7428#

Revision 1.6  2006/10/05 10:53:11  fernando
inclusão do uc-03.05.15

Revision 1.5  2006/07/05 20:41:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once ( CLA_TEXTBOX_SELECT );

class ITextBoxSelectEntidadeUsuario extends TextBoxSelect
{
function ITextBoxSelectEntidadeUsuario( $boValidaEncerramento=false )
{
    parent::TextBoxSelect();

    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                     );
    $this->obREntidade =  new ROrcamentoEntidade();
    if( $boValidaEncerramento ){
         $this->obREntidade->listarEntidadeRestos( $rsEntidades , " ORDER BY cod_entidade" );
    }else{
        $this->obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
    }

    $this->setRotulo              ( "Entidade"              );
    $this->setName                ( "inCodEntidade"         );
    $this->setTitle               ( "Selecione a entidade." );
    $this->setMensagem            ( "Entidade inválida"     );

    $this->obTextBox->setName        ( "inCodEntidade"             );
    $this->obTextBox->setId          ( "inCodEntidade"             );

    if ($rsEntidades->getNumLinhas()==1) {
         $this->inCodEntidade = $rsEntidades->getCampo('cod_entidade');
    }

    $this->obTextBox->setRotulo      ( "Entidade"                     );
    $this->obTextBox->setTitle       ( "Selecione a Entidade"         );
    $this->obTextBox->setInteiro     ( true                           );
    $this->obTextBox->setNull        ( false                          );

    $this->obSelect->setName          ( "stNomEntidade"               );
    $this->obSelect->setId            ( "stNomEntidade"               );
    if ($rsEntidades->getNumLinhas()>1) {
        $this->obSelect->addOption              ( "", "Selecione"      );
    }
    $this->obSelect->setCampoId       ( "cod_entidade"                 );
    $this->obSelect->setCampoDesc     ( "nom_cgm"                      );
    $this->obSelect->setStyle         ( "width: 520"                   );
    $this->obSelect->preencheCombo    ( $rsEntidades                   );
    $this->obSelect->setNull          ( false                          );

}

function setCodEntidade($inValor)
{
    $this->inCodEntidade = $inValor;
}

function montaHTML()
{
    $this->obTextBox->setValue       ( $this->inCodEntidade              );
    $this->obSelect->setValue         ( $this->inCodEntidade              );
     parent::montaHTML();
}
}
?>
