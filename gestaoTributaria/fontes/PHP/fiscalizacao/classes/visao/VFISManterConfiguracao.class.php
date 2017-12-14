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
    * Página de formulário do Manter configuracao
    * Data de Criacao: 30/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore
*/

include_once ( CAM_GT_FIS_NEGOCIO."RFISConfiguracao.class.php" );

class VFISManterConfiguracao
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function consultar($param)
    {
        return $this->controller->consultar($param);
    }

    public function configurar($param)
    {
        return $this->controller->configurar($param);
    }

    public function descricao($codNorma)
    {
        return $this->controller->descricao($codNorma);
    }

    public function nomeIndicador($codIndicador)
    {
        return $this->controller->nomeIndicador($codIndicador);
    }

}
