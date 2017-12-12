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
    * Classe de Mapeamento da tabela LRFTCERS_MODELO_LRF
    * Data de Criação   : 16/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Souza
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso uc-02.05.01, uc-02.01.35

    * @ignore
*/

/*
$Log$
Revision 1.10  2006/10/27 19:37:52  cako
Bug #6773#

Revision 1.9  2006/08/25 17:59:23  fernando
Bug #6773#

Revision 1.8  2006/08/25 17:48:17  fernando
Bug #6755#

Revision 1.7  2006/07/05 20:44:36  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLRFTCERSModeloLRF extends Persistente
{
    public function TLRFTCERSModeloLRF()
    {
        parent::Persistente();
        $this->setTabela('tcers.modelo_lrf');
        $this->setCampoCod('cod_modelo');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio','varchar',true,'4',true,false);
        $this->AddCampo('cod_modelo','integer',true,'',true,false);
        $this->AddCampo('nom_modelo','varchar',true,'80',false,false);
        $this->AddCampo('nom_modelo_orcamento','varchar',true,'80',false,false);

    }

}
