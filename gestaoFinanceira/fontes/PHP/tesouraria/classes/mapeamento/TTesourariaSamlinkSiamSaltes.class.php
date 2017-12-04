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
    * Classe de mapeamento da tabela SALTES do SIAM
    * Data de Criação: 10/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.17
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Classe de mapeamento da tabela SALTES do SIAM
  * Data de Criação: 10/03/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class TTesourariaSamlinkSiamSaltes extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaSamlinkSiamSaltes()
{
    parent::Persistente();
    $this->setTabela("SALTES");

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'k13_conta', 'integer', true,'', false, false );
    $this->AddCampo( 'k13_saldo', 'numeric', true,'', false, false );
    $this->AddCampo( 'k13_term',  'bpchar' , true,'15', false, false );
    $this->AddCampo( 'k13_vlratu','numeric', true,'', false, false );
    $this->AddCampo( 'k13_datvlr','date'   , true,'', false, false );
    $this->AddCampo( 'k13_plano', 'integer', true,'', false, false );
}
}
