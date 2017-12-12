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
    * Classe de mapeamento da tabela FN_REL_MODELOS_EXECUTIVO
    * Data de Criação: 12/05/2005

    * @author Desenvolvedor: Diego Lemos Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso  uc-02.05.01
 */

/*
$Log$
Revision 1.6  2006/07/05 20:44:36  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLRFTCERSContaReceitaModeloLRF extends Persistente
{
    public function TLRFTCERSContaReceitaModeloLRF()
    {
        parent::Persistente();
        $this->setTabela(LRFTCERS_CONTA_RECEITA_MODELO_LRF);
        $this->setComplementoChave('exercicio,cod_conta,cod_quadro,cod_modelo');

        $this->AddCampo('exercicio',    'varchar',      true,   '4',    true,   true);
        $this->AddCampo('cod_conta',    'integer',      true,   '',     true,   true);
        $this->AddCampo('cod_quadro',   'integer',      true,   '',     true,   true);
        $this->AddCampo('cod_modelo',   'integer',      true,   '',     true,   true);
        $this->AddCampo('ordem',        'integer',      true,   '',     false,  false);
    }

}
