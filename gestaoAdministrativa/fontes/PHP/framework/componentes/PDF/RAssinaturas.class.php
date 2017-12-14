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
 * Classe de Assinaturas Configuraveis para documentos PDF
 * Data de Criação: 14/11/2007

 * @author Analista: Anderson Cako Konze
 * @author Desenvolvedor: Leopoldo Braga Barreiro

 $Id: RAssinaturas.class.php 59612 2014-09-02 12:00:51Z gelson $
 TODO: Incluir o Caso de Uso no cabeçalho
 * Casos de uso: uc-

 */

if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', CAM_FPDF.'fonts/');
}

include_once( CAM_FPDF . "fpdf.php");

class RAssinaturas extends fpdf
{
    /**
     * @var Array
     * @access Private
     */
    public $arAssinaturas;

    /**
     *@var Integer
     *@access Private
     */
    public $inAssinaturasPorLinha; // quantidade de assinaturas por linha

    /**
     *@var Integer
     *@access Private
     */
    public $inNomeAlturaFonte;

    /**
     *@var Integer
     *@access Private
     */
    public $inCargoAlturaFonte;

    /**
     *@var Integer
     *@access Private
     */
    public $inBrancoAlturaCabecalho;

    /**
     *@var String
     *@access Private
     */
    public $stNomeEstiloFonte;

    /**
     *@var String
     *@access Private
     */
    public $stCargoEstiloFonte;

    /**
     * @var Array
     * @access Private
     */
    public $arPapeisDisponiveis;

    /**
     * @var String
     * @access Private
     */
    public $stTipoDocumento;

    // Métodos

    /**
     * @access Public
     * @param integer
     */
    public function setAssinaturasPorLinha($inValor)
    {
        $this->inAssinaturasPorLinha = $inValor;
    }

    /**
     * @access Public
     * @param Integer
     */
    public function setNomeAlturaFonte($valor=10)
    {
        $this->inNomeAlturaFonte = $valor;
    }

    /**
     * @access Public
     * @param Integer
     */
    public function setCargoAlturaFonte($valor=9)
    {
        $this->inCargoAlturaFonte = $valor;
    }

    /**
     * @access Public
     * @param Integer
     */
    public function setBrancoAlturaCabecalho($valor=10)
    {
        $this->inBrancoAlturaCabecalho = $valor;
    }

    /**
     * @access Public
     * @param String
     */
    public function setNomeEstiloFonte($valor='')
    {
        $this->stNomeEstiloFonte = $valor;
    }

    /**
     * @access Public
     * @param String
     */
    public function setCargoEstiloFonte($valor='')
    {
        $this->stCargoEstiloFonte = $valor;
    }

    /**
     * @access Public
     * @param Array
     */
    public function setArAssinaturas($arValor)
    {
        if (count($arValor) < $this->inAssinaturasPorLinha ) {
            $this->inAssinaturasPorLinha = count($arValor);
        }
        $x = $this->inAssinaturasPorLinha;
        $inLinhas = (int) ceil( count($arValor) / $x );

        $arAssinantes = array();
        $s = 1;
        for ( $w=0; $w<count($arValor); $w++ ) {
            if ($s == 1) {
                $arBranco = array();
                $arTraco = array();
                $arNome = array();
                $arCargo = array();
            }
            $arBranco[] = '';
            $arTraco[] = '__________________________';
            $arNome[] = $arValor[$w]['stNomCGM'];
            $arCargo[] = $arValor[$w]['stCargo'];
            if ($s == $this->inAssinaturasPorLinha || $w == (count($arValor)-1) ) {
                $rsBlocoAssina = new RecordSet;
                //$rsBlocoAssina->add( $arBranco );
                //$rsBlocoAssina->add( $arBranco );
                $rsBlocoAssina->add( $arTraco );
                $rsBlocoAssina->add( $arNome );
                $rsBlocoAssina->add( $arCargo );
                $arFormatacao = array();
                for ($c=0; $c<$this->inAssinaturasPorLinha; $c++) {
                    $arFormatacao[$c][0]['alturaFonte'] = 0;
                    $arFormatacao[$c][0]['estilo'] = '';
                    $arFormatacao[$c][1]['alturaFonte'] = 10;
                    $arFormatacao[$c][1]['estilo'] = '';
                    $arFormatacao[$c][2]['alturaFonte'] = 14;
                    $arFormatacao[$c][2]['estilo'] = '';
                    $arFormatacao[$c][3]['alturaFonte'] = 8;
                    $arFormatacao[$c][3]['estilo'] = '';
                }
                $rsBlocoAssina->arFormatacao = $arFormatacao;
                $arAssinantes[] = $rsBlocoAssina;
                $s = 0;
            }
            $s++;
        }
        $this->arAssinaturas = $arAssinantes;
    }

    public function getArAssinaturas() { return $this->arAssinaturas; }

    /**
     * Método Construtor
     * @access Private
     * @return void
     */
    public function RAssinaturas()
    {
        $this->stValor = '';
        $this->arAssinaturas = array();
        $this->inAssinaturasPorLinha = 3;
        $this->inNomeAlturaFonte = 10;
        $this->inCargoAlturaFonte = 9;
        $this->stNomeEstiloFonte = '';
        $this->stCargoEstiloFonte = '';
        $this->inBrancoAlturaCabecalho = 10;
    }

    /**
     * Montar Assinaturas em documento PDF
     * @access Public
     * @param  Object $obPDF Parâmetro Objeto PDF
     * @return void
     */
    public function montaPDF(&$obPDF)
    {
        $rsVazio = new RecordSet;
        $stAlinhamento = $obPDF->getAlinhamento();
        $nomeClasse = strtolower(get_class( $obPDF ));
        $arAssinaturas = $this->arAssinaturas;
        $inLarg = (int) ( 100 / $this->inAssinaturasPorLinha );
        $inTamFonte = $this->inNomeAlturaFonte;
        $stEstilo = $this->stNomeEstiloFonte;
        foreach ($arAssinaturas as $rsBlocoAssina) {
            $obPDF->addRecordSet( $rsVazio );
            $obPDF->setComponenteAgrupado( true );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento( "C" );
            $obPDF->setAlturaCabecalho( 4 );
            $obPDF->addCabecalho( '', $inLarg, $inTamFonte, '', '', '0' );
            $obPDF->addCampo( '', $inTamFonte, $stEstilo, '', '0');
            $obPDF->addRecordSet( $rsBlocoAssina );
            $obPDF->setComponenteAgrupado( true );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento( "C" );
            $obPDF->setAlturaCabecalho( 0 );
            for ($col=0; $col<($rsBlocoAssina->inNumColunas); $col++) {
                /* if ($this->inAssinaturasPorLinha > $rsBlocoAssina->inNumColunas) {
                 $inLarg = (int) ( 100 / $rsBlocoAssina->inNumColunas );
                 } else {
                 $inLarg = (int) ( 100 / $this->inAssinaturasPorLinha );
                 } */
                if ($nomeClasse == 'listaformpdf') {
                    $obPDF->addCabecalho( '', $inLarg, $inTamFonte, '', '', '0' );
                    $obPDF->addCampo( $col, $inTamFonte, $stEstilo, '', '0');
                } else {
                    $obPDF->addCabecalho( '', $inLarg, $inTamFonte);
                    $obPDF->addCampo($col, $inTamFonte, $stEstilo);
                }
            }
        }
        // Restaura valores originais
        $obPDF->setAlinhamento($stAlinhamento);
    }

    /**
     * Incluir Assinaturas em Documento Autorização de Empenho
     * @access Public
     * @param Array
     * @return void
     */

    /*
     Formato de $arValor:
     'inId'=>'',
     'inCodEntidade'=>inteiro código de entidade,
     'inCGM'=>inteiro numcgm,
     'stNomCGM'=>String nome pessoa,
     'stCargo'=>String Cargo,
     'stCRC'=>String CRC,
     'inPosAssinatura'=>inteiro Número Posicional da Assinatura
     */

    public function montaAutorizacaoEmpenho($arValor)
    {
        $mixedAssinaturas59 = Sessao::read('mixedAssinaturas59');
        if ( isset($mixedAssinaturas59) ) {
            $rsAssinaturas = $mixedAssinaturas59;
            $arAssinaturas = array();
            foreach ($arValor as $arAssinaturaSel) {
                if ($arAssinaturaSel['inPosAssinatura'] == 1) {
                    $arAssinaturas['autorizo'] = $arAssinaturaSel['stNomCGM'];
                }
                if ($arAssinaturaSel['inPosAssinatura'] == 2) {
                    $arAssinaturas['empenho'] = $arAssinaturaSel['stNomCGM'];
                }
            }
            $rsAssinaturas->add( $arAssinaturas );
            $arAssinaturas = array();
            foreach ($arValor as $arAssinaturaSel) {
                if ($arAssinaturaSel['inPosAssinatura'] == 1) {
                    $arAssinaturas['autorizo'] = $arAssinaturaSel['stCargo'];
                }
                if ($arAssinaturaSel['inPosAssinatura'] == 2) {
                    $arAssinaturas['empenho'] = $arAssinaturaSel['stCargo'];
                }
            }
            $rsAssinaturas->add( $arAssinaturas );
            $mixedAssinaturas59 = $rsAssinaturas;
            Sessao::write('mixedAssinaturas59',$mixedAssinaturas59);
        }
    }

    /**
     * Incluir Assinaturas em Documento Nota de Empenho
     * @access Public
     * @param  Array
     * @return void
     */

    /*
     Formato de $arValor:
     'inId'=>'',
     'inCodEntidade'=>inteiro código de entidade,
     'inCGM'=>inteiro numcgm,
     'stNomCGM'=>String nome pessoa,
     'stCargo'=>String Cargo,
     'stCRC'=>String CRC,
     'inPosAssinatura'=>inteiro Número Posicional da Assinatura
     */
    public function montaNotaEmpenho($arValor)
    {
        $arSelecionada = array();
        foreach ($arValor as $arAssinaSel) {
            $arSelecionada[$arAssinaSel['inPosAssinatura']] = array('stNomCGM'=> $arAssinaSel['stNomCGM'], 'stCargo'=>$arAssinaSel['stCargo']);
        }
        $mixedAssinaturas12 = Sessao::read('mixedAssinaturas12');
        if ( isset($mixedAssinaturas12)) {
            $rsOriginal = $mixedAssinaturas12;
            $arColunaOrdenador = $arColunaContador = $arColunaPague = array();
            // Popula com valores em branco as 10 linhas da coluna
            for ($i=0; $i<9; $i++) {
                $arColunaOrdenador[$i] = $arColunaContador[$i] = $arColunaPague[$i] = '';
            }
            // Captura linha 1 original
            $rsOriginal->setCorrente(1);
            $arItem = $rsOriginal->getObjeto();
            $arColunaOrdenador[0] = $arItem['autorizo'];
            $arColunaContador[0] = $arItem['contadoria'];
            $arColunaPague[0] = '';
            // Ordenador : coluna Autorizo
            $arColunaOrdenador[5] = '______________________________';
            if ( isset( $arSelecionada[1] ) ) {
                $arColunaOrdenador[6] = $arSelecionada[1]['stNomCGM'];
                $arColunaOrdenador[7] = $arSelecionada[1]['stCargo'];
            } else {
                $arColunaOrdenador[6] = 'Ordenador da Despesa';
            }
            // Conferido : coluna Contadoria
            $arColunaContador[2] = '______________________________';
            if ( isset ( $arSelecionada[2] ) ) {
                $arColunaContador[3] = $arSelecionada[2]['stNomCGM'];
                $arColunaContador[4] = $arSelecionada[2]['stCargo'];
            } else {
                $arColunaContador[3] = 'Conferido';
            }
            // Contador : coluna Contadoria
            $arColunaContador[6] = '______________________________';
            if ( isset ( $arSelecionada[3] ) ) {
                $arColunaContador[7] = $arSelecionada[3]['stNomCGM'];
                $arColunaContador[8] = $arSelecionada[3]['stCargo'];
            } else {
                $arColunaContador[7] = 'Contador';
            }
            // Pague-se : coluna Pague
            $arColunaPague[5] = '______________________________';
            if ( isset ( $arSelecionada[4] ) ) {
                $arColunaPague[6] = $arSelecionada[4]['stNomCGM'];
                $arColunaPague[7] = $arSelecionada[4]['stCargo'];
            } else {
                $arColunaPague[6] = '';
            }
            $rsAssinatura = new RecordSet;
            for ($i=0; $i<count($arColunaOrdenador); $i++) {
                $arItem = array(
                                    'autorizo'		=> $arColunaOrdenador[$i],
                                    'contadoria'	=> $arColunaContador[$i],
                                    'pague'		=> $arColunaPague[$i]
                );
                $rsAssinatura->add( $arItem );
            }
            #sessao->transf[12] = $rsAssinatura;
            Sessao::write('mixedAssinaturas12',$mixedAssinaturas12);
        }
    }

    /**
     * Incluir Assinaturas em Documento Ordem de Pagamento
     * @access Public
     * @param Array
     * @return Void
     */

    /*
     Formato de $arValor:
     'inId'=>'',
     'inCodEntidade'=>inteiro código de entidade,
     'inCGM'=>inteiro numcgm,
     'stNomCGM'=>String nome pessoa,
     'stCargo'=>String Cargo,
     'stCRC'=>String CRC,
     'inPosAssinatura'=>inteiro Número Posicional da Assinatura
     */

    public function montaOrdemPagamento($arValor)
    {
        $arSelecionada = array();
        foreach ($arValor as $arAssinaSel) {
            $arSelecionada[$arAssinaSel['inPosAssinatura']] = array('stNomCGM'=> $arAssinaSel['stNomCGM'], 'stCargo'=>$arAssinaSel['stCargo']);
        }

        if ( isset($arSelecionada[2]) || isset($arSelecionada[1]) ) {
            $rsDados = Sessao::read('mixedAssinaturas3');#sessao->transf[3];
            $arValor = $rsDados->arElementos;
            $arItem = array();
            if ( isset($arSelecionada[1]) ) {
                $arItem[0][2] = $arSelecionada[1]['stNomCGM'];
                $arItem[1][2] = $arSelecionada[1]['stCargo'];
            }
            if ( isset($arSelecionada[2]) ) {
                $arItem[0][4] = $arSelecionada[2]['stNomCGM'];
                $arItem[1][4] = $arSelecionada[2]['stCargo'];
            }
            $arValor[1] = $arItem[0];
            $arValor[2] = $arItem[1];
            $rsAssinatura = new RecordSet;
            $rsAssinatura->preenche($arValor);
            #sessao->transf[3] = $rsAssinatura;
            Sessao::write('mixedAssinaturas3',$rsAssinatura);
            unset($rsDados, $arValor);
        }
        if ( isset($arSelecionada[3]) ) {
            $rsDados = Sessao::read('mixedAssinaturas6');#sessao->transf[6];
            $arValor = $rsDados->arElementos;
            $arItem = array();
            $arValor[5][2] = '                                    ' . $arSelecionada[3]['stNomCGM'];
            $arValor[6][2] = '                                    ' . $arSelecionada[3]['stCargo'];
            $rsAssinatura = new RecordSet;
            $rsAssinatura->preenche($arValor);
            #sessao->transf[6] = $rsAssinatura;
            Sessao::write('mixedAssinaturas6',$rsAssinatura);
        }
    }

    /**
     * Incluir Assinaturas em Recibo de Despesa Extra
     * @access Public
     * @param Array, obPDF
     * @return Void
     */

    /*
     Formato de $arValor:
     'inId'=>'',
     'inCodEntidade'=>inteiro código de entidade,
     'inCGM'=>inteiro numcgm,
     'stNomCGM'=>String nome pessoa,
     'stCargo'=>String Cargo,
     'stCRC'=>String CRC,
     'inPosAssinatura'=>inteiro Número Posicional da Assinatura
     */

    public function montaReciboDespesaExtra($arValor, &$obPDF)
    {
        $arSelecionada = array();
        foreach ($arValor as $arAssinaSel) {
            $arSelecionada[$arAssinaSel['inPosAssinatura']] = array('stNomCGM'=> $arAssinaSel['stNomCGM'], 'stCargo'=>$arAssinaSel['stCargo']);
        }
        $arAssinatura = array();
        $arAssinatura[] = array(
                            'autorizo'	=> '',
                            'contadoria'=> 'Data',
                            'pague'		=> '       Banco:________________________________________________'
                            );
                            $arAssinatura[] = array(
                            'autorizo'	=> '',
                            'contadoria'=> '',
                            'pague'		=> ''
                            );

                            if ( isset($arSelecionada[1]) ) {
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[1]['stNomCGM'],
                                'contadoria'=> '',
                                'pague'		=> ''
                                );
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[1]['stCargo'],
                                'contadoria'=> '____/____/____',
                                'pague'		=> '       Número do Documento                                    Cheque'
                                );
                            } else {
                                $arAssinatura[] = array(
                                'autorizo'	=> 'Conferido',
                                'contadoria'=> '____/____/____',
                                'pague'		=> '       Número do Documento                                    Cheque'
                                );
                            }

                            $arAssinatura[] = array(
                            'autorizo'	=> '',
                            'contadoria'=> '',
                            'pague'		=> '       ____________________________                 _________________'
                            );
                            $arAssinatura[] = array(
                            'autorizo'	=> '',
                            'contadoria'=> '',
                            'pague'		=> ''
                            );

                            if ( isset($arSelecionada[2]) && isset($arSelecionada[3]) ) {
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[2]['stNomCGM'],
                                'contadoria'=> $arSelecionada[3]['stNomCGM'],
                                'pague'		=> ''
                                );
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[2]['stCargo'],
                                'contadoria'=> $arSelecionada[3]['stCargo'],
                                'pague'		=> '       ____/____/____             __________________________________'
                                );
                            } elseif ( isset($arSelecionada[2]) && !isset($arSelecionada[3]) ) {
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[2]['stNomCGM'],
                                'contadoria'=> 'Ordenador(a) da Despesa',
                                'pague'		=> ''
                                );
                                $arAssinatura[] = array(
                                'autorizo'	=> $arSelecionada[2]['stCargo'],
                                'contadoria'=> '',
                                'pague'		=> '       ____/____/____             __________________________________'
                                );
                            } elseif ( !isset($arSelecionada[2]) && isset($arSelecionada[3]) ) {
                                $arAssinatura[] = array(
                                'autorizo'	=> 'Contador(a)',
                                'contadoria'=> $arSelecionada[3]['stNomCGM'],
                                'pague'		=> ''
                                );
                                $arAssinatura[] = array(
                                'autorizo'	=> '',
                                'contadoria'=> $arSelecionada[3]['stCargo'],
                                'pague'		=> '       ____/____/____             __________________________________'
                                );
                            } else {
                                $arAssinatura[] = array(
                                'autorizo'	=> 'Contador(a)',
                                'contadoria'=> 'Ordenador(a) da Despesa',
                                'pague'		=> '       ____/____/____             __________________________________'
                                );
                            }

                            if ( isset($arSelecionada[4]) ) {
                                $arAssinatura[] = array(
                                'autorizo'	=> '',
                                'contadoria'=> '',
                                'pague'		=> '                                                                ' . $arSelecionada[4]['stNomCGM']
                                );
                                $arAssinatura[] = array(
                                'autorizo'	=> '',
                                'contadoria'=> '',
                                'pague'		=> '                                                                ' . $arSelecionada[4]['stCargo']
                                );
                            } else {
                                $arAssinatura[] = array(
                                'autorizo'	=> '',
                                'contadoria'=> '',
                                'pague'		=> '                                                                    Tesoureiro(a)'
                                );
                            }

                            $rsAssinatura = new RecordSet;
                            $rsAssinatura->preenche($arAssinatura);

                            $obPDF->addRecordSet            ( $rsAssinatura );
                            $obPDF->setAlturaCabecalho      ( 3 );
                            $obPDF->setQuebraPaginaLista    ( false );
                            $obPDF->setAlinhamento          ( "C" );
                            $obPDF->addCabecalho            ( "CONTADORIA GERAL" , 25, 9, 'B', '', 'LR');
                            $obPDF->addCabecalho            ( "PAGUE-SE"         , 25, 9, 'B', '', '');
                            $obPDF->addCabecalho            ( "TESOURARIA"       , 50, 9, 'B', '', 'LR');
                            $obPDF->addCampo                ( "autorizo"   , 7.5, '', '', 'LR');
                            $obPDF->addCampo                ( "contadoria" , 7.5, '', '', 'LR');
                            $obPDF->setAlinhamento          ( "L" );
                            $obPDF->addCampo                ( "pague"      , 7.5, '', '', 'LR');
    }

    /**
     * Incluir Assinaturas em Recibo de Receita Extra
     * @access Public
     * @param Array, Array
     * @return Void
     */

    /*
     Formato de $arValor:
     'inId'=>'',
     'inCodEntidade'=>inteiro código de entidade,
     'inCGM'=>inteiro numcgm,
     'stNomCGM'=>String nome pessoa,
     'stCargo'=>String Cargo,
     'stCRC'=>String CRC,
     'inPosAssinatura'=>inteiro Número Posicional da Assinatura
     */
    public function montaReciboReceitaExtra($arValor, &$arVazio)
    {
        $arSelecionada = array();
        foreach ($arValor as $arAssinaSel) {
            $arSelecionada[$arAssinaSel['inPosAssinatura']] = array('stNomCGM'=> $arAssinaSel['stNomCGM'], 'stCargo'=>$arAssinaSel['stCargo']);
        }

        if (isset($arSelecionada[1])) {
            if (isset($arSelecionada[1]['stNomCGM'])) {
                $arVazio[] = array( 'nome'=>$arSelecionada[1]['stNomCGM'], 'titulo'=>'' );
            }
            if (isset($arSelecionada[1]['stCargo'])) {
                $arVazio[] = array( 'nome'=>$arSelecionada[1]['stCargo'], 'titulo'=>'' );
            }
        }
    }

    /**
     * Definir Tipo de Documento que será gerado
     * @access Public
     * @param String
     * @return Void
     */

    /* Método relacionado ao método de mesmo nome da Classe IMontaAssinaturas */

    public function definePapeisDisponiveis($stTipoDoc)
    {
        if ($stTipoDoc != '') {
            $this->stTipoDocumento = $stTipoDoc;
        }
        switch ($this->stTipoDocumento) {
            case 'autorizacao_empenho':
                $this->arPapeisDisponiveis = array( 0=>'', 1=>'Autorizo', 2=>'Autorizo o Empenho' );
                break;
            case 'nota_empenho':
                $this->arPapeisDisponiveis = array( 0=>'', 1=>'Ordenador da Despesa', 2=>'Conferido', 3=>'Contador', 4=>'Pague-se' );
                break;
            case 'ordem_pagamento':
                $this->arPapeisDisponiveis = array( 0=>'', 1=>'Visto', 2=>'Ordenador de Despesa', 3=>'Tesoureiro' );
                break;
            case 'recibo_despesa_extra':
                $this->arPapeisDisponiveis = array( 0=>'', 1=>'Conferido', 2=>'Contador', 3=>'Ordenador da Despesa', 4=>'Tesoureiro' );
                break;
            case 'recibo_receita_extra':
                $this->arPapeisDisponiveis = array( 0=>'', 1=>'Tesoureiro' );
                break;
        }
        Sessao::write('papeis',$this->arPapeisDisponiveis );
    }

}
