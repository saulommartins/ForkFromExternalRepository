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
    * Data de Criação: 10/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26063 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-11 18:31:04 -0300 (Qui, 11 Out 2007) $

    * Casos de uso : uc-03.05.22
*/

/*
$Log$
Revision 1.1  2007/10/11 21:30:32  girardi
adicionando ao repositório (rescisão de contrato e aditivos de contrato)

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoContratoAditivosAnulacao extends Persistente
{

    /**
    * Método Construtor
    * @access Private
    */
    public function TLicitacaoContratoAditivosAnulacao()
    {
        parent::Persistente();
        $this->setTabela("licitacao.contrato_aditivos_anulacao");

        $this->setCampoCod('num_aditivo');
        $this->setComplementoChave('num_contrato, exercicio, cod_entidade, exercicio_contrato');

        $this->AddCampo('num_aditivo', 'integer', true, '', true, false);
        $this->AddCampo('num_contrato', 'integer', true, '', true, true);
        $this->AddCampo('exercicio_contrato', 'char', true, '4', true, true);
        $this->AddCampo('exercicio', 'char', true, '4', true, false);
        $this->AddCampo('cod_entidade', 'integer', true, '', true, true);
        $this->AddCampo('dt_anulacao', 'date', true, '', false, false);
        $this->AddCampo('motivo', 'char', true, '100', false, false);
        $this->AddCampo('valor_anulacao', 'numeric', true, '14,2', false, false);
    }
}
