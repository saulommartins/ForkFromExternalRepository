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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 09/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.03.00

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBElementoDePara extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */

    public function TTPBElementoDePara()
    {
        parent::Persistente();
        $this->setTabela("tcepb.elemento_de_para");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_conta');

        $this->AddCampo( 'exercicio' ,'varchar' ,true, '4' ,true ,true );
        $this->AddCampo( 'cod_conta' ,'integer' ,true, ''  ,true ,true );
        $this->AddCampo( 'estrutural','varchar' ,true, '150', false, true );
    }

    /*function recuperaOrgao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="") {
        return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgao()
    {
    }
    */
}
