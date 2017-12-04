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
 * Extensão da Classe de mapeamento
 * Data de Criação: 2/02/2012

 * @author Analista: Carlos Adriano
 * @author Desenvolvedor: Carlos Adriano

 * @package URBEM
 * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPECodigoFonteRecurso extends Persistente
{

    function TTCEPECodigoFonteRecurso()
    {
        parent::Persistente();
        $this->setTabela('tcepe.codigo_fonte_recurso');
    
        $this->setCampoCod('');
        $this->setComplementoChave('cod_recurso, exercicio');
    
        $this->AddCampo('cod_recurso' , 'integer', true, ''  , true  , true);
        $this->AddCampo('exercicio'   , 'varchar', true, '4' , true  , true);
        $this->AddCampo('cod_fonte'   , 'integer', true, ''  , false , true);
    }

}
