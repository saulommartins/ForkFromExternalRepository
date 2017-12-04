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
    * Classe de mapeamento da tabela ORCAMENTO.DESPESA
    * Data de Criação: 29/07/2014

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
    * Classe de mapeamento da tabela ORCAMENTO.DESPESA
    * Data de Criação: 29/07/2014

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

*/
class TTCEPBModalidadeDespesa extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEPBModalidadeDespesa()
    {
        parent::Persistente();
        $this->setTabela('tcepb.modalidade_despesa');
    
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_modalidade');
    
        $this->AddCampo('exercicio'      ,'char'    ,true ,'4',true ,false);
        $this->AddCampo('cod_modalidade' ,'integer' ,true ,'' ,true ,false);
        $this->AddCampo('modalidade'     ,'varchar' ,true ,'' ,false,false);
    }
}