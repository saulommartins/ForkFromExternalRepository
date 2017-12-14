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
 * Classe de Mapeamento para a tabela sw_copia_digital
 * Data de Criação: 02/09/2014
 * @author Analista: Gelson
 * @author Desenvolvedor: Evandro Melos
 * $id :
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TProtocoloCopiaDigital extends Persistente
{

    public function TProtocoloCopiaDigital()
    {
        parent::Persistente();
        $this->setTabela('sw_copia_digital');
        $this->setComplementoChave('cod_copia,cod_documento,cod_processo,exercicio');

        $this->AddCampo('cod_copia'     ,'integer' ,true ,'' ,true  ,false);
        $this->AddCampo('cod_documento' ,'integer' ,true ,'' ,true  ,true);
        $this->AddCampo('cod_processo'  ,'integer' ,true ,'' ,true  ,true);
        $this->AddCampo('exercicio'     ,'varchar' ,true ,'' ,true  ,true);
        $this->AddCampo('imagem'        ,'boolean' ,true ,'' ,false ,false);
        $this->AddCampo('anexo'         ,'text'    ,true ,'' ,false ,false);
        
    }

}