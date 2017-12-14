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
 * Classe de regra impressoraCheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */
include_once ( CAM_GF_TES_MAPEAMENTO.'TTesourariaBancoChequeLayout.class.php'        );
include_once ( CAM_GF_TES_MAPEAMENTO.'TTesourariaChequeImpressoraTerminal.class.php' );
include_once ( CAM_GF_TES_NEGOCIO.'RTesourariaTerminal.class.php'                    );
include_once ( CAM_GA_ADM_MAPEAMENTO.'TAdministracaoImpressora.class.php'            );
include_once ( CAM_GF_TES_MAPEAMENTO.'TTesourariaUsuarioTerminal.class.php'          );

class RTesourariaImpressoraCheque
{
    public $obTTesourariaBancoChequeLayout,
           $obRTesourariaTerminal,
           $obTTesourariaChequeImpressoraTerminal,
           $obTTesourariaUsuarioTerminal,
           $inCodImpressora,
           $stFilaImpressao;

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTTesourariaBancoChequeLayout        = new TTesourariaBancoChequeLayout();
        $this->obRTesourariaTerminal                 = new RTesourariaTerminal();
        $this->obTTesourariaChequeImpressoraTerminal = new TTesourariaChequeImpressoraTerminal();
        $this->obTTesourariaUsuarioTerminal          = new TTesourariaUsuarioTerminal();
    }

    /**
     * Método privado getLayout, busca o layout do banco
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param integer $inCodBanco
     *
     * @return object $rsLayout
     */
    private function getLayout($inCodBanco)
    {
        //Seta os dados no mapeamento
        $this->obTTesourariaBancoChequeLayout->setDado                   ('cod_banco', $inCodBanco);
        $obErro = $this->obTTesourariaBancoChequeLayout->recuperaPorChave($rsLayout               );

        return $rsLayout;
    }

    /**
     * Método privado buildLayout, monta a string a ser impressa no cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return string $stCheque
     */
    private function buildLayout($obCheque)
    {
        //Recupera o layout do cheque
        $rsLayout = $this->getLayout($obCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);

        //Recupera o nome da prefeitura
        $obCheque->obRAdministracaoConfiguracao->inExercicio = Sessao::getExercicio();
        $obCheque->obRAdministracaoConfiguracao->consultarMunicipio();

        //Preenche o array com espacos em branco
        $arCheque = array();
        for ($i = 1; $i <= 22; $i++) {
            $arCheque[$i] = '';
        }

        //Monta a linha do valor numerico
        $arCheque[$rsLayout->getCampo('lin_valor_numerico')] = str_repeat(' ',$rsLayout->getCampo('col_valor_numerico')). number_format($obCheque->flValor,2,',','.');

        //Monta a linha do valor por extenso
        $stValorExtenso = SistemaLegado::extenso($obCheque->flValor,true);
        $stValorExtenso = strtr($stValorExtenso, 'áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ', 'aaaaeeiooouucAAAAEEIOOOUUC');

        $inTotalCaracteres = strlen($stValorExtenso) + $rsLayout->getCampo('col_extenso_1');
        $stValorExtensoLinha1 = str_repeat(' ',$rsLayout->getCampo('col_extenso_1'));
        $stValorExtensoLinha2 = str_repeat(' ',$rsLayout->getCampo('col_extenso_2'));
        if ($inTotalCaracteres > 96) {
            $arValorExtenso = explode(' ',$stValorExtenso);
            $stValorExtenso = '';
            foreach ($arValorExtenso as $stValor) {
                $stValorExtenso .= $stValor . ' ';
                if ((strlen($stValorExtenso) + $rsLayout->getCampo('col_extenso_1')) > 96) {
                    $stValorExtensoLinha2 .= $stValor . ' ';
                } else {
                    $stValorExtensoLinha1 .= $stValor . ' ';
                }
            }
        } else {
            $stValorExtensoLinha1 = str_repeat(' ',$rsLayout->getCampo('col_extenso_1')) . $stValorExtenso;
        }

        $arCheque[$rsLayout->getCampo('lin_extenso_1')] = $stValorExtensoLinha1;
        $arCheque[$rsLayout->getCampo('lin_extenso_2')] = $stValorExtensoLinha2;

        //Monta o nome do credor
        $stFavorecido = strtr($obCheque->obREmpenhoOrdemPagamento->obREmpenhoEmpenho->obRCGM->stNomCGM, 'áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ', 'aaaaeeiooouucAAAAEEIOOOUUC');
        $arCheque[$rsLayout->getCampo('lin_favorecido')] = str_repeat(' ', $rsLayout->getCampo('col_favorecido')) . ucwords(strtolower($stFavorecido));

        //Monta a linha da cidade/data
        $arCheque[$rsLayout->getCampo('lin_cidade_data')] = str_repeat(' ', 96);
        //Recupera a data e divide ela
        $arMes = array(  1 => 'Janeiro'
                        ,2 => 'Fevereiro'
                        ,3 => 'Março'
                        ,4 => 'Abril'
                        ,5 => 'Maio'
                        ,6 => 'Junho'
                        ,7 => 'Julho'
                        ,8 => 'Agosto'
                        ,9 => 'Setembro'
                       ,10 => 'Outubro'
                       ,11 => 'Novembro'
                       ,12 => 'Dezembro' );
        $arData = explode('/',$obCheque->stDtEmissao);
        $arData[1] = $arMes[(int) $arData[1]];
        //cidade
        $stCidade = strtr($obCheque->obRAdministracaoConfiguracao->stNomMunicipio, 'áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ', 'aaaaeeiooouucAAAAEEIOOOUUC');
        $arCheque[$rsLayout->getCampo('lin_cidade_data')] = substr_replace($arCheque[$rsLayout->getCampo('lin_cidade_data')], $obCheque->obRAdministracaoConfiguracao->stNomMunicipio, $rsLayout->getCampo('col_cidade'), strlen($stCidade));
        //dia
        $arCheque[$rsLayout->getCampo('lin_cidade_data')] = substr_replace($arCheque[$rsLayout->getCampo('lin_cidade_data')], $arData[0] ,$rsLayout->getCampo('col_dia'), strlen($arData[0]));
        //mes
        $arCheque[$rsLayout->getCampo('lin_cidade_data')] = substr_replace($arCheque[$rsLayout->getCampo('lin_cidade_data')], $arData[1] ,$rsLayout->getCampo('col_mes'), strlen($arData[1]));
        //ano
        $arCheque[$rsLayout->getCampo('lin_cidade_data')] = substr_replace($arCheque[$rsLayout->getCampo('lin_cidade_data')], $arData[2] ,$rsLayout->getCampo('col_ano'), strlen($arData[2]));

        $stCheque = implode("\n",$arCheque);

        $stTMP = tempnam('/tmp','cheque_');
        $handle = fopen($stTMP,'w');
        fwrite($handle, $stCheque);
        fclose($handle);

        return $stTMP;
    }

    /**
     * Método privado buildLayoutVerso, monta a string a ser impressa no verso do cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return string $stCheque
     */
    private function buildLayoutVerso($obCheque)
    {
        $stCheque  = "\n";
        $stCheque .= str_repeat(' ',20) . $obCheque->stDescricao;
        $stCheque = strtr($stCheque, 'áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ', 'aaaaeeiooouucAAAAEEIOOOUUC');

        $stTMP = tempnam('/tmp','cheque_');
        $handle = fopen($stTMP,'w');
        fwrite($handle, $stCheque);
        fclose($handle);

        return $stTMP;
    }

    /**
     * Método printCheque, manda o cheque para a impressora
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return void
     */
    public function printCheque($obCheque)
    {
        $stCheque = $this->buildLayout($obCheque);
        $this->findImpressoraTerminal();

        system("lpr -P " . $this->stFilaImpressao . ' ' . $stCheque ,$return_var);

    }

    /**
     * Método printChequeVerso, manda o cheque para a impressora para imprimir o verso
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return void
     */
    public function printChequeVerso($obCheque)
    {
        $stCheque = $this->buildLayoutVerso($obCheque);
        $this->findImpressoraTerminal();

        system("lpr -P " . $this->stFilaImpressao . ' ' . $stCheque ,$return_var);
    }

    /**
     * Método que retorna as impressoras cadastradas no sistema
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return void
     */
    public function listImpressorasSistema(&$rsImpressora)
    {
        $obTAdministracaoImpressora = new TAdministracaoImpressora();
        $obErro = $obTAdministracaoImpressora->recuperaTodos($rsImpressora);
        
        return $obErro;
    }

    /**
     * Método que retorna a impressora de cheque vinculada ao terminal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return object $obErro
     */
    public function findImpressoraTerminal()
    {
        if ($this->obRTesourariaTerminal->stMac != '') {
            $stFiltro .= " terminal.cod_verificador = '" . $this->obRTesourariaTerminal->stMac . "' AND ";
        }
        if ($this->obRTesourariaTerminal->inCodTerminal != '') {
            $stFiltro .= " terminal.cod_terminal = '" . $this->obRTesourariaTerminal->inCodTerminal . "' AND ";
        }
        if ($this->obRTesourariaTerminal->stTimestampTerminal != '') {
            $stFiltro .= " terminal.timestamp_terminal = '" . $this->obRTesourariaTerminal->stTimestampTerminal . "' AND ";
        }

        if ($stFiltro) {
            $stFiltro = 'WHERE ' . substr($stFiltro, 0, -4);
        }

        $obErro = $this->obTTesourariaChequeImpressoraTerminal->findImpressoraTerminal($rsImpressora,$stFiltro);
        
        $this->stFilaImpressao = $rsImpressora->getCampo('fila_impressao');
        $this->inCodImpressora = $rsImpressora->getCampo('cod_impressora');

        return $obErro;
    }

    /**
     * Método que inclui a impressora ao terminal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return void
     */
    public function insertImpressoraTerminal()
    {
        $this->obTTesourariaChequeImpressoraTerminal->setDado ('cod_terminal'       , $this->obRTesourariaTerminal->inCodTerminal      );
        $this->obTTesourariaChequeImpressoraTerminal->setDado ('timestamp_terminal' , $this->obRTesourariaTerminal->stTimestampTerminal);
        $this->obTTesourariaChequeImpressoraTerminal->setDado ('cod_impressora'     , $this->inCodImpressora                           );

        $obErro = $this->removeImpressoraTerminal();
        
        if (!$obErro->ocorreu()) {
            $obErro = $this->obTTesourariaChequeImpressoraTerminal->inclusao();
        }

        return $obErro;
    }

    /**
     * Método que remove a impressora vinculada a um terminal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obCheque
     *
     * @return void
     */
    public function removeImpressoraTerminal()
    {
        $stComplementoChave = $this->obTTesourariaChequeImpressoraTerminal->getComplementoChave();
        $this->obTTesourariaChequeImpressoraTerminal->setComplementoChave('cod_terminal,timestamp_terminal');

        $this->obTTesourariaChequeImpressoraTerminal->setDado ('cod_terminal'       , $this->obRTesourariaTerminal->inCodTerminal      );
        $this->obTTesourariaChequeImpressoraTerminal->setDado ('timestamp_terminal' , $this->obRTesourariaTerminal->stTimestampTerminal);

        $obErro = $this->obTTesourariaChequeImpressoraTerminal->exclusao();

        $this->obTTesourariaChequeImpressoraTerminal->setComplementoChave($stComplementoChave);

        return $obErro;
    }
    
    /**
     * Método que seta os valores de codigo e timestamp do terminal para vincular uma impressora ao terminal
     *
     * @author      Analista        
     * @author      Desenvolvedor   Arthur Cruz <arthur.cruz@cnm.org.br>
     *
     * @return object $obErro
     */
    public function recuperaCodigoTimestampTerminal($inCGM)
    {
        $this->obTTesourariaUsuarioTerminal->setDado('cgm_usuario', $inCGM);
        
        $obErro = $this->obTTesourariaUsuarioTerminal->recuperaCodigoTimestamp($rsCodigoTimestamp);
        
        $this->obRTesourariaTerminal->inCodTerminal       = $rsCodigoTimestamp->getCampo('cod_terminal');
        $this->obRTesourariaTerminal->stTimestampTerminal = $rsCodigoTimestamp->getCampo('timestamp_terminal');

        return $obErro;
    }

}
