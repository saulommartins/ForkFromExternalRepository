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
    * Classe de mapeamento da tabela tcesc.motivo_licensa_esfinge
    * Data de Criação: 04/05/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-05-07 16:41:15 -0300 (Seg, 07 Mai 2007) $

    * Casos de uso: uc-02.08.18
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCESCMotivoLicencaEsfinge extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TExportacaoTCESCMotivoLicencaEsfinge()
    {
        parent::Persistente();
        $this->setTabela('tcesc.motivo_licenca_esfinge');

        $this->setCampoCod('cod_motivo_licenca_esfinge');
        $this->setComplementoChave('');

        $this->AddCampo('cod_motivo_licenca_esfinge', 'integer', true, ''  , true , false );
        $this->AddCampo('descricao'                 , 'char'   , true, '30', false, false );
    }

}

?>
