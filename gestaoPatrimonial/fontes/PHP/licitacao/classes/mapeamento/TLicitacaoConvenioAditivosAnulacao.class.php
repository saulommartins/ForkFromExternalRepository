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
    * Data de Criação: 16/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26126 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-16 17:23:35 -0200 (Ter, 16 Out 2007) $

    * Casos de uso : uc-03.05.29
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoConvenioAditivosAnulacao extends Persistente
{

    /**
    * Método Construtor
    * @access Private
    */
    public function TLicitacaoConvenioAditivosAnulacao()
    {
        parent::Persistente();
        $this->setTabela("licitacao.convenio_aditivos_anulacao");

        $this->setCampoCod('num_aditivo');
        $this->setComplementoChave('num_convenio, exercicio, exercicio_convenio');

        $this->AddCampo('num_aditivo', 'integer', true, '', true, false);
        $this->AddCampo('num_convenio', 'integer', true, '', true, true);
        $this->AddCampo('exercicio_convenio', 'char', true, '4', true, true);
        $this->AddCampo('exercicio', 'char', true, '4', true, false);
        $this->AddCampo('dt_anulacao', 'date', true, '', false, false);
        $this->AddCampo('motivo', 'char', true, '100', false, false);
    }
}
