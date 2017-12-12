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
* Arquivo de popup de busca de Credor
* Data de Criação: 23/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: Jose Eduardo Porto

* @package URBEM
* @subpackage

$Revision: 30668 $
$Name$
$Author: luciano $
$Date: 2007-02-15 15:27:07 -0200 (Qui, 15 Fev 2007) $

* Casos de uso: uc-02.03.00, uc-02.03.29 , uc-02.04.32
*/

/*
$Log$
Revision 1.7  2007/02/15 17:25:16  luciano
#8385#

Revision 1.6  2007/02/15 12:13:53  tonismar
bug #8385

Revision 1.5  2006/10/24 18:29:26  domluc
Correção Bug #7041#
Adicionado caso de uso

Revision 1.4  2006/09/25 12:14:55  cleisson
Bug #7033#

Revision 1.3  2006/08/29 14:50:05  fernando
Alteração do hint do componente para ficar de acordo com o padrão do framework

Revision 1.2  2006/07/05 20:46:47  cleisson
Adicionada tag Log aos arquivos

*/

include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");

class  IPopUpCredor extends IPopUpCGM
{
/**
    * @access Private
    * @var Object
*/

var $obForm;
var $inNumCGM;

/**
    * Metodo Construtor
    * @access Public

*/

function IPopUpCredor(&$obForm)
{
    parent::IPopUpCGM($obForm);

    $this->obForm = $obForm;

    $this->setRotulo                 ( 'Credor'        );
    $this->setTitle                  ( 'Informe o Credor.'  );
    $this->setId                     ( 'stNomCredor'   );
    $this->setNull                   ( false           );

    $this->obCampoCod->setName       ( "inCodCredor"   );
    $this->obCampoCod->setSize       ( 10              );
    $this->obCampoCod->setMaxLength  ( 10              );
    $this->obCampoCod->setAlign      ( "left"          );

    $this->stTipo = 'geral';
}

}
?>
