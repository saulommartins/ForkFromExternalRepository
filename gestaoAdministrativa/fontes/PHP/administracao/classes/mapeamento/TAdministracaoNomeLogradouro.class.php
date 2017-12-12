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
* Classe de mapeamento para administracao.nome_logradouro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TNomeLogradouro extends Persistente
{
function TNomeLogradouro()
{
    parent::Persistente();
    $this->setTabela('sw_nome_logradouro');
    $this->setCampoCod('cod_logradouro');
    $this->setComplementoChave ('timestamp,nom_logradouro');
    
    $this->AddCampo('cod_logradouro', 'integer'  , true , '', true , true  );
    $this->AddCampo('timestamp'     , 'timestamp', false, '', true , false );
    $this->AddCampo('cod_tipo'      , 'integer'  , true , '', false, true  );
    $this->AddCampo('nom_logradouro', 'varchar'  , true , 60, false, false );
    $this->AddCampo('dt_inicio'     , 'date'     , false, '', false, false );
    $this->AddCampo('dt_fim'        , 'date'     , false, '', false, false );
    $this->AddCampo('cod_norma'     , 'integer'  , true , '', false, true  );
}

}
